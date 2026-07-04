<?php
/**
 * Universal CMS — Installation Wizard
 * Self-contained (runs before config.php exists). Multi-step:
 *   1. Requirements   2. Database   3. Site + Admin   4. Install
 *
 * Delete the /install directory after a successful install.
 */
declare(strict_types=1);
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('ROOT', dirname(__DIR__));
$configFile = ROOT . '/config/config.php';

$step   = (int) ($_GET['step'] ?? 1);

// Already installed? Stop — but always allow the final success screen so the
// post-install redirect (which writes config.php) can show its confirmation.
if (file_exists($configFile) && empty($_GET['force']) && $step !== 5) {
    exit('<p style="font:16px sans-serif;padding:40px">CMS is already installed. Delete <code>/config/config.php</code> to re-run the installer, or remove the <code>/install</code> folder.</p>');
}

$errors = [];
$ok     = [];

/* ---------- Requirement checks ---------- */
function requirements(): array
{
    return [
        'PHP >= 8.0'            => version_compare(PHP_VERSION, '8.0.0', '>='),
        'PDO MySQL extension'   => extension_loaded('pdo_mysql'),
        'mbstring extension'    => extension_loaded('mbstring'),
        'fileinfo extension'    => extension_loaded('fileinfo'),
        'GD or Imagick'         => extension_loaded('gd') || extension_loaded('imagick'),
        '/config writable'      => is_writable(ROOT . '/config') || is_writable(ROOT),
        '/uploads writable'     => ensure_writable(ROOT . '/uploads'),
        '/storage writable'     => ensure_writable(ROOT . '/storage'),
    ];
}
function ensure_writable(string $dir): bool
{
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    return is_writable($dir);
}

/* ---------- Step 2: test DB connection ---------- */
if ($step === 2 && ($_POST['action'] ?? '') === 'testdb') {
    $_SESSION['db'] = [
        'host' => trim($_POST['db_host'] ?? 'localhost'),
        'port' => trim($_POST['db_port'] ?? '3306'),
        'name' => trim($_POST['db_name'] ?? ''),
        'user' => trim($_POST['db_user'] ?? ''),
        'pass' => $_POST['db_pass'] ?? '',
    ];
    try {
        $db = $_SESSION['db'];
        $pdo = new PDO(
            "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
            $db['user'], $db['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $ok[] = 'Database connection successful.';
        header('Location: ?step=3'); exit;
    } catch (Throwable $e) {
        $errors[] = 'Connection failed: ' . $e->getMessage();
    }
}

/* ---------- Step 3 -> 4: run install ---------- */
if ($step === 4 && ($_POST['action'] ?? '') === 'install') {
    $_SESSION['site'] = [
        'site_name'   => trim($_POST['site_name'] ?? 'My Website'),
        'admin_name'  => trim($_POST['admin_name'] ?? ''),
        'admin_email' => trim($_POST['admin_email'] ?? ''),
        'admin_pass'  => $_POST['admin_pass'] ?? '',
        'template'    => $_POST['template'] ?? 'corporate',
    ];
    $db   = $_SESSION['db']   ?? null;
    $site = $_SESSION['site'];

    // Validate
    if (!$db) { $errors[] = 'Database details missing — restart the wizard.'; }
    if (!filter_var($site['admin_email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid admin email is required.'; }
    if (strlen($site['admin_pass']) < 8) { $errors[] = 'Admin password must be at least 8 characters.'; }
    if ($site['admin_name'] === '') { $errors[] = 'Admin name is required.'; }

    if (!$errors) {
        try {
            $pdo = new PDO(
                "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
                $db['user'], $db['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // 1. Schema
            run_sql_file($pdo, __DIR__ . '/schema.sql');
            // 2. Seed
            run_sql_file($pdo, __DIR__ . '/seed.sql');

            // 3. Admin user
            $hash = password_hash($site['admin_pass'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (name,email,password,role,is_active) VALUES (?,?,?,'super_admin',1)
                 ON DUPLICATE KEY UPDATE password=VALUES(password), role='super_admin'"
            );
            $stmt->execute([$site['admin_name'], $site['admin_email'], $hash]);

            // 4. Site name setting
            $pdo->prepare("UPDATE settings SET value=? WHERE `key`='site_name'")->execute([$site['site_name']]);
            $pdo->prepare("UPDATE settings SET value=? WHERE `key`='meta_title'")->execute([$site['site_name']]);

            // 5. Home page from chosen template blueprint
            $tpl = $pdo->prepare("SELECT id, blueprint FROM templates WHERE slug=?");
            $tpl->execute([$site['template']]);
            $template = $tpl->fetch(PDO::FETCH_ASSOC);

            $pdo->prepare(
                "INSERT INTO pages (title,slug,template_id,status,is_home,meta_title)
                 VALUES ('Home','home',?,'published',1,?)"
            )->execute([$template['id'] ?? null, $site['site_name']]);
            $pageId = (int) $pdo->lastInsertId();

            // Build starter sections from blueprint
            $sections = json_decode($template['blueprint'] ?? '[]', true) ?: [];
            $order = 0;
            $secStmt = $pdo->prepare(
                "INSERT INTO page_sections (page_id,type,title,data,settings,sort_order)
                 VALUES (?,?,?,?,?,?)"
            );
            foreach ($sections as $s) {
                $secStmt->execute([
                    $pageId, $s['type'], $s['title'] ?? ucfirst($s['type']),
                    json_encode(default_section_data($s['type'])),
                    json_encode(['padding' => 'lg']),
                    $order++,
                ]);
            }

            // 6. Primary menu items
            $menuId = (int) $pdo->query("SELECT id FROM menus WHERE slug='primary'")->fetchColumn();
            $mi = $pdo->prepare("INSERT INTO menu_items (menu_id,label,url,sort_order) VALUES (?,?,?,?)");
            foreach ([['Home','/'],['About','/about'],['Blog','/blog'],['Contact','/contact']] as $i => $item) {
                $mi->execute([$menuId, $item[0], $item[1], $i]);
            }

            // 7. Write config.php
            write_config($db, $site);

            // 8. Write robots + .htaccess hints already in place
            $_SESSION['installed'] = true;
            session_write_close();
            header('Location: ?step=5'); exit;

        } catch (Throwable $e) {
            $errors[] = 'Installation error: ' . $e->getMessage();
        }
    }
}

/* ---------- helpers ---------- */
function run_sql_file(PDO $pdo, string $file): void
{
    $sql = file_get_contents($file);

    // Strip full-line SQL comments so they don't break naive statement splitting.
    $lines = preg_split('/\r?\n/', $sql);
    $clean = [];
    foreach ($lines as $line) {
        if (preg_match('/^\s*--/', $line)) continue;   // comment line
        $clean[] = $line;
    }
    $sql = implode("\n", $clean);

    // Split on semicolons (schema/seed contain no stored procedures).
    foreach (explode(';', $sql) as $stmt) {
        $stmt = trim($stmt);
        if ($stmt !== '') {
            $pdo->exec($stmt);
        }
    }
}

function default_section_data(string $type): array
{
    return match ($type) {
        'hero' => [
            'heading' => 'Welcome to Our Website',
            'subheading' => 'A modern, fully editable website built with Universal CMS.',
            'button_text' => 'Get Started', 'button_link' => '/contact',
            'button2_text' => '', 'button2_link' => '',
            'background_image' => '', 'overlay_color' => 'rgba(15,23,42,0.55)',
            'align' => 'center', 'height' => 'large',
        ],
        'text' => ['heading' => 'About Us', 'body' => '<p>Tell your story here. This text is fully editable from the admin panel.</p>'],
        'cards' => ['heading' => 'Our Highlights', 'columns' => 3, 'items' => [
            ['title' => 'Feature One', 'text' => 'Describe it.', 'icon' => 'fa-star', 'image' => '', 'link' => ''],
            ['title' => 'Feature Two', 'text' => 'Describe it.', 'icon' => 'fa-heart', 'image' => '', 'link' => ''],
            ['title' => 'Feature Three', 'text' => 'Describe it.', 'icon' => 'fa-bolt', 'image' => '', 'link' => ''],
        ]],
        'gallery' => ['heading' => 'Gallery', 'images' => []],
        'testimonials' => ['heading' => 'What People Say', 'items' => [
            ['name' => 'Happy Customer', 'role' => 'Client', 'quote' => 'Outstanding service!', 'avatar' => '', 'rating' => 5],
        ]],
        'faq' => ['heading' => 'Frequently Asked Questions', 'items' => [
            ['q' => 'Is everything editable?', 'a' => 'Yes — every section is managed from the admin panel.'],
        ]],
        'pricing' => ['heading' => 'Pricing', 'items' => [
            ['name' => 'Basic', 'price' => '19', 'period' => '/mo', 'features' => "Feature A\nFeature B", 'button_text' => 'Choose', 'button_link' => '#', 'featured' => false],
        ]],
        'cta' => ['heading' => 'Ready to get started?', 'subheading' => 'Join us today.', 'button_text' => 'Contact Us', 'button_link' => '/contact'],
        'contact' => ['heading' => 'Get in Touch', 'form_slug' => 'contact', 'show_info' => true],
        'map' => ['heading' => 'Find Us', 'embed' => '', 'address' => ''],
        'image' => ['image' => '', 'caption' => '', 'align' => 'center'],
        'html' => ['html' => '<!-- custom HTML -->'],
        default => [],
    };
}

function write_config(array $db, array $site): void
{
    $appKey = bin2hex(random_bytes(16));
    $tpl = <<<PHP
<?php
/**
 * Universal CMS — Environment Configuration
 * Generated by the installer. Keep this file private.
 */

// ---- Database ----
define('DB_HOST',    '%s');
define('DB_PORT',    '%s');
define('DB_NAME',    '%s');
define('DB_USER',    '%s');
define('DB_PASS',    '%s');
define('DB_CHARSET', 'utf8mb4');

// ---- Application ----
define('APP_NAME',     '%s');
define('APP_DEBUG',    false);          // set true while developing
define('APP_KEY',      '%s');
define('APP_TIMEZONE', 'UTC');

PHP;
    $content = sprintf(
        $tpl,
        addslashes($db['host']), addslashes($db['port']), addslashes($db['name']),
        addslashes($db['user']), addslashes($db['pass']),
        addslashes($site['site_name']), $appKey
    );
    if (!is_dir(ROOT . '/config')) { mkdir(ROOT . '/config', 0755, true); }
    file_put_contents(ROOT . '/config/config.php', $content);
}

/* ====================================================================== */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$db = $_SESSION['db'] ?? ['host'=>'localhost','port'=>'3306','name'=>'','user'=>'','pass'=>''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install — Universal CMS</title>
<style>
  :root{--p:#2563eb;--d:#0f172a;--g:#f1f5f9;--b:#e2e8f0}
  *{box-sizing:border-box;margin:0;padding:0}
  body{font:15px/1.55 system-ui,Segoe UI,Roboto,sans-serif;background:linear-gradient(135deg,#1e293b,#0f172a);color:#0f172a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
  .card{background:#fff;border-radius:18px;box-shadow:0 25px 60px rgba(0,0,0,.35);width:100%;max-width:680px;overflow:hidden}
  .head{background:var(--d);color:#fff;padding:26px 32px}
  .head h1{font-size:22px;font-weight:700}
  .head p{opacity:.7;font-size:13px;margin-top:4px}
  .steps{display:flex;background:#0b1220}
  .steps span{flex:1;text-align:center;padding:10px;font-size:12px;color:#64748b;border-top:3px solid transparent}
  .steps span.on{color:#fff;border-top-color:var(--p)}
  .body{padding:32px}
  h2{font-size:18px;margin-bottom:16px}
  label{display:block;font-weight:600;font-size:13px;margin:14px 0 5px}
  input,select{width:100%;padding:11px 13px;border:1px solid var(--b);border-radius:10px;font-size:14px}
  input:focus,select:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px rgba(37,99,235,.15)}
  .row{display:flex;gap:14px}.row>div{flex:1}
  .btn{display:inline-block;background:var(--p);color:#fff;border:0;padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;margin-top:22px}
  .btn:hover{filter:brightness(1.08)}
  .req{list-style:none}.req li{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--g)}
  .pass{color:#16a34a;font-weight:600}.fail{color:#dc2626;font-weight:600}
  .alert{padding:12px 14px;border-radius:10px;margin-bottom:14px;font-size:13px}
  .alert.err{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
  .alert.ok{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
  .tpl-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:8px}
  .tpl-grid label{display:block;margin:0;border:2px solid var(--b);border-radius:10px;padding:14px;text-align:center;cursor:pointer;font-size:13px}
  .tpl-grid input{display:none}
  .tpl-grid input:checked + span{color:var(--p)}
  .tpl-grid label:has(input:checked){border-color:var(--p);background:#eff6ff}
  small.hint{color:#64748b;font-size:12px}
  code{background:var(--g);padding:2px 6px;border-radius:5px;font-size:12px}
</style>
</head>
<body>
<div class="card">
  <div class="head"><h1>Universal CMS — Setup Wizard</h1><p>Install your website builder in a few steps.</p></div>
  <div class="steps">
    <span class="<?= $step>=1?'on':'' ?>">1 · Requirements</span>
    <span class="<?= $step>=2?'on':'' ?>">2 · Database</span>
    <span class="<?= $step>=3?'on':'' ?>">3 · Site &amp; Admin</span>
    <span class="<?= $step>=5?'on':'' ?>">4 · Finish</span>
  </div>
  <div class="body">

  <?php foreach ($errors as $e): ?><div class="alert err"><?= h($e) ?></div><?php endforeach; ?>
  <?php foreach ($ok as $o): ?><div class="alert ok"><?= h($o) ?></div><?php endforeach; ?>

  <?php if ($step === 1): $reqs = requirements(); $allPass = !in_array(false, $reqs, true); ?>
    <h2>System Requirements</h2>
    <ul class="req">
      <?php foreach ($reqs as $name => $pass): ?>
        <li><span><?= h($name) ?></span><span class="<?= $pass?'pass':'fail' ?>"><?= $pass?'✓ OK':'✗ Missing' ?></span></li>
      <?php endforeach; ?>
    </ul>
    <?php if ($allPass): ?>
      <a class="btn" href="?step=2">Continue →</a>
    <?php else: ?>
      <p style="margin-top:16px"><small class="hint">Resolve the items marked ✗ (enable PHP extensions in cPanel → Select PHP Version, and set folder permissions to 755) then refresh.</small></p>
      <a class="btn" href="?step=1">Re-check</a>
    <?php endif; ?>

  <?php elseif ($step === 2): ?>
    <h2>Database Connection</h2>
    <small class="hint">Create a MySQL database &amp; user in cPanel → MySQL Databases, then enter the details below.</small>
    <form method="post" action="?step=2">
      <input type="hidden" name="action" value="testdb">
      <div class="row">
        <div><label>Host</label><input name="db_host" value="<?= h($db['host']) ?>" required></div>
        <div><label>Port</label><input name="db_port" value="<?= h($db['port']) ?>"></div>
      </div>
      <label>Database Name</label><input name="db_name" value="<?= h($db['name']) ?>" required>
      <label>Database User</label><input name="db_user" value="<?= h($db['user']) ?>" required>
      <label>Database Password</label><input type="password" name="db_pass" value="<?= h($db['pass']) ?>">
      <button class="btn" type="submit">Test &amp; Continue →</button>
    </form>

  <?php elseif ($step === 3 || $step === 4): ?>
    <h2>Site &amp; Administrator</h2>
    <form method="post" action="?step=4">
      <input type="hidden" name="action" value="install">
      <label>Website Name</label>
      <input name="site_name" value="<?= h($_POST['site_name'] ?? 'My Website') ?>" required>

      <label>Choose a Starter Template</label>
      <div class="tpl-grid">
        <?php foreach (['corporate'=>'Corporate','hotel'=>'Hotel','restaurant'=>'Restaurant','ecommerce'=>'E-commerce','portfolio'=>'Portfolio','blog'=>'Blog','realestate'=>'Real Estate','services'=>'Services','blank'=>'Blank'] as $val=>$lbl): ?>
          <label><input type="radio" name="template" value="<?= $val ?>" <?= ($val==='corporate')?'checked':'' ?>><span><?= $lbl ?></span></label>
        <?php endforeach; ?>
      </div>

      <hr style="margin:22px 0;border:none;border-top:1px solid var(--b)">
      <label>Admin Full Name</label><input name="admin_name" value="<?= h($_POST['admin_name'] ?? '') ?>" required>
      <label>Admin Email</label><input type="email" name="admin_email" value="<?= h($_POST['admin_email'] ?? '') ?>" required>
      <label>Admin Password</label><input type="password" name="admin_pass" minlength="8" required>
      <small class="hint">Minimum 8 characters. You'll use this to log in at <code>/admin</code>.</small>
      <br><button class="btn" type="submit">Install Now →</button>
    </form>

  <?php elseif ($step === 5): ?>
    <h2>🎉 Installation Complete!</h2>
    <div class="alert ok">Your Universal CMS is ready.</div>
    <p style="margin:12px 0"><strong>Important security step:</strong> delete the <code>/install</code> folder now.</p>
    <ul style="margin:12px 0 12px 18px;font-size:14px;line-height:1.9">
      <li>Admin panel: <a href="../admin">/admin</a></li>
      <li>Your website: <a href="../">/</a></li>
    </ul>
    <a class="btn" href="../admin">Go to Admin Login →</a>
  <?php endif; ?>

  </div>
</div>
</body>
</html>

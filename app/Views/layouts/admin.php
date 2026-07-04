<?php
use App\Core\Auth;
use App\Models\Setting;
use App\Models\FormEntry;

$role = Auth::role();
$userName = \App\Core\Session::get('user_name', 'Admin');
$siteName = Setting::get('site_name', 'Universal CMS');
$unread = FormEntry::unreadCount();

$nav = [
  ['Dashboard', 'admin', 'fa-gauge-high', '*'],
  ['Pages', 'admin/pages', 'fa-file-lines', 'pages.edit'],
  ['Blog Posts', 'admin/posts', 'fa-newspaper', 'posts.manage'],
  ['Categories', 'admin/categories', 'fa-tags', 'posts.manage'],
  ['Products', 'admin/products', 'fa-box', 'products.manage'],
  ['Orders', 'admin/orders', 'fa-bag-shopping', 'products.manage'],
  ['Media Library', 'admin/media', 'fa-photo-film', 'media.manage'],
  ['Menus', 'admin/menus', 'fa-bars', 'menus.manage'],
  ['Forms', 'admin/forms', 'fa-list-check', 'forms.view'],
  ['Users & Roles', 'admin/users', 'fa-users', '*'],
  ['Settings', 'admin/settings', 'fa-gear', '*'],
];
$uri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'Admin') ?> · <?= e($siteName) ?></title>
  <meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-shell">

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-brand">
      <i class="fa fa-cube"></i> <span><?= e($siteName) ?></span>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($nav as [$label, $path, $icon, $ability]):
        if ($ability !== '*' && !Auth::can($ability) && !Auth::can('*')) continue;
        $active = ($path === 'admin') ? preg_match('#/admin/?$#', $uri) : (strpos($uri, $path) !== false); ?>
        <a href="<?= base_url($path) ?>" class="<?= $active ? 'active' : '' ?>">
          <i class="fa <?= $icon ?>"></i><span><?= e($label) ?></span>
          <?php if ($path === 'admin/forms' && $unread): ?><span class="nav-badge"><?= $unread ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <a href="<?= base_url('/') ?>" target="_blank"><i class="fa fa-arrow-up-right-from-square"></i> View Site</a>
    </div>
  </aside>

  <!-- Main -->
  <div class="admin-main">
    <header class="admin-topbar">
      <button class="btn-burger" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fa fa-bars"></i></button>
      <h1 class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
      <div class="topbar-right dropdown">
        <button class="user-chip" data-bs-toggle="dropdown">
          <span class="avatar-circle"><?= strtoupper(substr($userName,0,1)) ?></span>
          <span class="d-none d-md-inline"><?= e($userName) ?></span>
          <i class="fa fa-chevron-down small"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><span class="dropdown-item-text small text-muted"><?= e(ucwords(str_replace('_',' ',$role))) ?></span></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="<?= base_url('admin/settings') ?>"><i class="fa fa-gear me-2"></i>Settings</a></li>
          <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout') ?>"><i class="fa fa-right-from-bracket me-2"></i>Logout</a></li>
        </ul>
      </div>
    </header>

    <main class="admin-content">
      <?php foreach (($flashes ?? []) as $type => $messages):
        $cls = ['success'=>'success','error'=>'danger','info'=>'info','warning'=>'warning'][$type] ?? 'secondary';
        foreach ($messages as $m): ?>
          <div class="alert alert-<?= $cls ?> alert-dismissible fade show"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endforeach; endforeach; ?>

      <?= $content ?>
    </main>
  </div>
</div>

<?php require APP_PATH . '/Views/admin/partials/media-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>

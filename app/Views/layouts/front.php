<?php
/** @var array $content rendered view + shared layout vars */
$theme   = $theme   ?? [];
$site    = $site    ?? ['name' => 'Website', 'logo' => '', 'favicon' => ''];
$social  = $social  ?? [];
$contact = $contact ?? [];
$primaryMenu = $primaryMenu ?? [];
$footerMenu  = $footerMenu  ?? [];

$primary   = $theme['primary_color']   ?? '#2563eb';
$secondary = $theme['secondary_color'] ?? '#0f172a';
$accent    = $theme['accent_color']    ?? '#f59e0b';
$radius    = (int) ($theme['border_radius'] ?? 10);
$bodyFont  = $theme['body_font']    ?? 'Inter';
$headFont  = $theme['heading_font'] ?? 'Poppins';
$maxWidth  = (int) ($theme['layout_width'] ?? 1200);
$sticky    = (int) ($theme['sticky_header'] ?? 1) === 1;
$btnStyle  = $theme['button_style'] ?? 'solid';

$showHeader = $showHeader ?? true;
$showFooter = $showFooter ?? true;
$fonts = urlencode($bodyFont) . ':wght@400;500;600;700&family=' . urlencode($headFont) . ':wght@500;600;700;800';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? $site['name']) ?></title>
  <?php if (!empty($metaDescription)): ?><meta name="description" content="<?= e($metaDescription) ?>"><?php endif; ?>
  <?php if (!empty($metaKeywords)): ?><meta name="keywords" content="<?= e($metaKeywords) ?>"><?php endif; ?>
  <?php if (!empty($canonical)): ?><link rel="canonical" href="<?= e($canonical) ?>"><?php endif; ?>
  <meta property="og:title" content="<?= e($pageTitle ?? $site['name']) ?>">
  <?php if (!empty($metaDescription)): ?><meta property="og:description" content="<?= e($metaDescription) ?>"><?php endif; ?>
  <?php if (!empty($ogImage)): ?><meta property="og:image" content="<?= e(uploads_url($ogImage)) ?>"><?php endif; ?>
  <?php if (!empty($site['favicon'])): ?><link rel="icon" href="<?= e(uploads_url($site['favicon'])) ?>"><?php endif; ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=<?= $fonts ?>&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="<?= asset('css/front.css') ?>" rel="stylesheet">
  <style>
    :root{
      --c-primary:<?= e($primary) ?>;
      --c-secondary:<?= e($secondary) ?>;
      --c-accent:<?= e($accent) ?>;
      --radius:<?= $radius ?>px;
      --font-body:'<?= e($bodyFont) ?>',system-ui,sans-serif;
      --font-head:'<?= e($headFont) ?>',var(--font-body);
      --maxw:<?= $maxWidth ?>px;
    }
    .btn-theme{
      background:<?= $btnStyle==='outline' ? 'transparent' : 'var(--c-primary)' ?>;
      color:<?= $btnStyle==='outline' ? 'var(--c-primary)' : '#fff' ?>;
      border:2px solid var(--c-primary);
      border-radius:<?= $btnStyle==='pill' ? '50px' : 'var(--radius)' ?>;
    }
    .btn-theme:hover{background:var(--c-secondary);border-color:var(--c-secondary);color:#fff}
  </style>
</head>
<body class="cms-body">

<?php if ($showHeader): require APP_PATH . '/Views/front/partials/header.php'; endif; ?>

<main id="content"><?= $content ?></main>

<?php if ($showFooter): require APP_PATH . '/Views/front/partials/footer.php'; endif; ?>

<?php require APP_PATH . '/Views/front/partials/flashes.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/front.js') ?>"></script>
</body>
</html>

<?php
/**
 * Dynamic header — layout & options are fully controlled from
 * Admin → Settings → Header (no code editing required).
 */
$layout      = setting('header_layout', 'classic');     // classic|centered|split|minimal
$showTopbar  = (int) setting('header_show_topbar', 1) === 1;
$sticky      = (int) setting('sticky_header', setting('header_sticky', 1)) === 1;
$transparent = (int) setting('header_transparent', 0) === 1;
$container   = setting('header_container', 'boxed') === 'full' ? 'container-fluid px-4' : 'container';
$ctaText     = trim((string) setting('header_cta_text', ''));
$ctaLink     = setting('header_cta_link', '#');
$hbg         = trim((string) setting('header_bg', ''));
$htext       = trim((string) setting('header_text', ''));

$headStyle = [];
if ($hbg !== '')   { $headStyle[] = '--header-bg:' . $hbg; }
if ($htext !== '') { $headStyle[] = '--header-text:' . $htext; }

$renderMenu = function (array $items) use (&$renderMenu) {
    foreach ($items as $it):
        $hasKids = !empty($it['children']); ?>
        <li class="nav-item <?= $hasKids ? 'dropdown' : '' ?>">
          <a class="nav-link <?= $hasKids ? 'dropdown-toggle' : '' ?>"
             href="<?= e($it['url']) ?>" target="<?= e($it['target']) ?>"
             <?= $hasKids ? 'data-bs-toggle="dropdown"' : '' ?>>
            <?php if (!empty($it['icon'])): ?><i class="fa <?= e($it['icon']) ?> me-1"></i><?php endif; ?>
            <?= e($it['label']) ?>
          </a>
          <?php if ($hasKids): ?>
            <ul class="dropdown-menu">
              <?php foreach ($it['children'] as $c): ?>
                <li><a class="dropdown-item" href="<?= e($c['url']) ?>" target="<?= e($c['target']) ?>"><?= e($c['label']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </li>
    <?php endforeach;
};

$brand = function () use ($site) { ?>
  <a class="navbar-brand" href="<?= base_url('/') ?>">
    <?php if (!empty($site['logo'])): ?>
      <img src="<?= e(uploads_url($site['logo'])) ?>" alt="<?= e($site['name']) ?>" height="42">
    <?php else: ?>
      <span class="brand-text"><?= e($site['name']) ?></span>
    <?php endif; ?>
  </a>
<?php };

$cta = function () use ($ctaText, $ctaLink) {
    if ($ctaText !== ''): ?>
      <a href="<?= e($ctaLink) ?>" class="btn btn-theme btn-sm header-cta ms-lg-3"><?= e($ctaText) ?></a>
    <?php endif;
};
?>
<header class="site-header header-<?= e($layout) ?> <?= $sticky ? 'is-sticky' : '' ?> <?= $transparent ? 'is-transparent' : '' ?>"
        <?= $headStyle ? 'style="' . implode(';', $headStyle) . '"' : '' ?>>

  <?php if ($showTopbar && (!empty($contact['phone']) || !empty($contact['email']) || array_filter($social))): ?>
  <div class="topbar">
    <div class="<?= $container ?> d-flex justify-content-between align-items-center">
      <div class="small">
        <?php if (!empty($contact['phone'])): ?><span class="me-3"><i class="fa fa-phone me-1"></i><?= e($contact['phone']) ?></span><?php endif; ?>
        <?php if (!empty($contact['email'])): ?><span><i class="fa fa-envelope me-1"></i><?= e($contact['email']) ?></span><?php endif; ?>
      </div>
      <div class="social-icons">
        <?php foreach (['facebook'=>'fa-facebook-f','instagram'=>'fa-instagram','twitter'=>'fa-x-twitter','linkedin'=>'fa-linkedin-in','youtube'=>'fa-youtube'] as $k=>$icon):
          if (!empty($social[$k])): ?>
            <a href="<?= e($social[$k]) ?>" target="_blank" rel="noopener" aria-label="<?= $k ?>"><i class="fa-brands <?= $icon ?>"></i></a>
        <?php endif; endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($layout === 'centered'): /* ---- Logo centered, menu below ---- */ ?>
  <nav class="navbar navbar-expand-lg main-nav flex-column py-2">
    <div class="<?= $container ?> d-flex flex-column align-items-center">
      <div class="d-flex w-100 justify-content-between align-items-center d-lg-block text-lg-center">
        <?php $brand(); ?>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
      </div>
      <div class="collapse navbar-collapse justify-content-center" id="mainNav">
        <ul class="navbar-nav align-items-lg-center mt-2"><?php $renderMenu($primaryMenu); $cta(); ?></ul>
      </div>
    </div>
  </nav>

  <?php elseif ($layout === 'split'): /* ---- Menu split around centered logo ---- */
    $half = (int) ceil(count($primaryMenu) / 2);
    $left = array_slice($primaryMenu, 0, $half);
    $right = array_slice($primaryMenu, $half); ?>
  <nav class="navbar navbar-expand-lg main-nav">
    <div class="<?= $container ?>">
      <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto"><?php $renderMenu($left); ?></ul>
        <div class="mx-3 d-none d-lg-block"><?php $brand(); ?></div>
        <ul class="navbar-nav ms-auto align-items-lg-center"><?php $renderMenu($right); $cta(); ?></ul>
      </div>
      <div class="d-lg-none"><?php $brand(); ?></div>
    </div>
  </nav>

  <?php else: /* ---- classic / minimal: logo left, menu right ---- */ ?>
  <nav class="navbar navbar-expand-lg main-nav">
    <div class="<?= $container ?>">
      <?php $brand(); ?>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <?php $renderMenu($primaryMenu); $cta(); ?>
        </ul>
      </div>
    </div>
  </nav>
  <?php endif; ?>
</header>

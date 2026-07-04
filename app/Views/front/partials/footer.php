<?php
/**
 * Dynamic footer — layout & content controlled from
 * Admin → Settings → Footer (no code editing required).
 */
$year     = date('Y');
$layout   = setting('footer_layout', 'columns');         // columns|compact|simple|minimal
$fbg      = trim((string) setting('footer_bg', ''));
$ftext    = trim((string) setting('footer_text', ''));
$about    = trim((string) setting('footer_about', $site['tagline'] ?? ''));
$col2Title = setting('footer_col2_title', 'Quick Links');
$col3Title = setting('footer_col3_title', 'Get in Touch');
$showNews = (int) setting('footer_newsletter', 0) === 1;
$showSocial = (int) setting('footer_show_social', 1) === 1;
$copyright = (string) setting('footer_copyright', '');
$copyright = $copyright !== ''
    ? str_replace(['{year}', '{site}'], [$year, $site['name']], $copyright)
    : '© ' . $year . ' ' . $site['name'] . '. All rights reserved.';

$fStyle = [];
if ($fbg !== '')   { $fStyle[] = '--footer-bg:' . $fbg; }
if ($ftext !== '') { $fStyle[] = '--footer-text:' . $ftext; }

$socialBlock = function () use ($social) {
    if (!array_filter($social)) return; ?>
    <div class="social-icons mt-3">
      <?php foreach (['facebook'=>'fa-facebook-f','instagram'=>'fa-instagram','twitter'=>'fa-x-twitter','linkedin'=>'fa-linkedin-in','youtube'=>'fa-youtube'] as $k=>$icon):
        if (!empty($social[$k])): ?>
          <a href="<?= e($social[$k]) ?>" target="_blank" rel="noopener"><i class="fa-brands <?= $icon ?>"></i></a>
      <?php endif; endforeach; ?>
    </div>
<?php };

$brandBlock = function () use ($site, $about, $socialBlock, $showSocial) { ?>
  <?php if (!empty($site['logo'])): ?>
    <img src="<?= e(uploads_url($site['logo'])) ?>" alt="<?= e($site['name']) ?>" height="40" class="mb-3">
  <?php else: ?>
    <h5 class="footer-brand"><?= e($site['name']) ?></h5>
  <?php endif; ?>
  <?php if ($about !== ''): ?><p class="text-muted-2"><?= e($about) ?></p><?php endif; ?>
  <?php if ($showSocial) $socialBlock(); ?>
<?php };

$linksBlock = function () use ($footerMenu, $primaryMenu, $col2Title) { ?>
  <h6 class="footer-title"><?= e($col2Title) ?></h6>
  <ul class="footer-links">
    <?php foreach (($footerMenu ?: $primaryMenu) as $it): ?>
      <li><a href="<?= e($it['url']) ?>"><?= e($it['label']) ?></a></li>
    <?php endforeach; ?>
  </ul>
<?php };

$contactBlock = function () use ($contact, $col3Title) { ?>
  <h6 class="footer-title"><?= e($col3Title) ?></h6>
  <ul class="footer-contact">
    <?php if (!empty($contact['address'])): ?><li><i class="fa fa-location-dot me-2"></i><?= e($contact['address']) ?></li><?php endif; ?>
    <?php if (!empty($contact['phone'])): ?><li><i class="fa fa-phone me-2"></i><?= e($contact['phone']) ?></li><?php endif; ?>
    <?php if (!empty($contact['email'])): ?><li><i class="fa fa-envelope me-2"></i><?= e($contact['email']) ?></li><?php endif; ?>
  </ul>
<?php };

$newsletterBlock = function () use ($showNews) {
    if (!$showNews) return; ?>
  <h6 class="footer-title">Newsletter</h6>
  <p class="text-muted-2 small">Subscribe for updates.</p>
  <form class="footer-newsletter" method="post" action="<?= base_url('form/submit') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="form_slug" value="newsletter">
    <div class="input-group">
      <input type="email" name="email" class="form-control" placeholder="Your email" required>
      <button class="btn btn-theme" type="submit"><i class="fa fa-paper-plane"></i></button>
    </div>
  </form>
<?php };
?>
<footer class="site-footer footer-<?= e($layout) ?>" <?= $fStyle ? 'style="' . implode(';', $fStyle) . '"' : '' ?>>
  <div class="container">

    <?php if ($layout === 'minimal'): ?>
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-2">
        <div class="fw-bold"><?= e($site['name']) ?></div>
        <ul class="footer-links d-flex gap-3 mb-0 list-unstyled">
          <?php foreach (($footerMenu ?: $primaryMenu) as $it): ?><li><a href="<?= e($it['url']) ?>"><?= e($it['label']) ?></a></li><?php endforeach; ?>
        </ul>
        <?php if ($showSocial) $socialBlock(); ?>
      </div>

    <?php elseif ($layout === 'simple'): ?>
      <div class="text-center">
        <?php $brandBlock(); ?>
        <ul class="footer-links d-flex flex-wrap justify-content-center gap-3 mt-3 list-unstyled">
          <?php foreach (($footerMenu ?: $primaryMenu) as $it): ?><li><a href="<?= e($it['url']) ?>"><?= e($it['label']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>

    <?php elseif ($layout === 'compact'): ?>
      <div class="row g-4">
        <div class="col-lg-6"><?php $brandBlock(); ?></div>
        <div class="col-lg-3"><?php $linksBlock(); ?></div>
        <div class="col-lg-3"><?php $contactBlock(); ?></div>
      </div>

    <?php else: /* columns (default, 4-up with optional newsletter) */ ?>
      <div class="row g-4">
        <div class="col-lg-<?= $showNews ? '3' : '4' ?>"><?php $brandBlock(); ?></div>
        <div class="col-lg-<?= $showNews ? '3' : '4' ?>"><?php $linksBlock(); ?></div>
        <div class="col-lg-<?= $showNews ? '3' : '4' ?>"><?php $contactBlock(); ?></div>
        <?php if ($showNews): ?><div class="col-lg-3"><?php $newsletterBlock(); ?></div><?php endif; ?>
      </div>
    <?php endif; ?>

    <hr class="footer-divider">
    <div class="text-center small text-muted-2"><?= e($copyright) ?></div>
  </div>
</footer>

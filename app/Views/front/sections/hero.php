<?php
$d = $data;
$bgImg  = !empty($d['background_image']) ? uploads_url($d['background_image']) : '';
$overlay = $d['overlay_color'] ?? 'rgba(15,23,42,0.55)';
$align  = $d['align'] ?? 'center';
$height = ['small'=>'min-height:46vh','medium'=>'min-height:62vh','large'=>'min-height:82vh'][$d['height'] ?? 'large'] ?? 'min-height:82vh';
$alignCls = ['left'=>'text-start align-items-start','center'=>'text-center align-items-center','right'=>'text-end align-items-end'][$align] ?? 'text-center align-items-center';
?>
<div class="hero-wrap d-flex <?= $alignCls ?>"
     style="<?= $height ?>;<?= $bgImg ? "background-image:linear-gradient($overlay,$overlay),url('".e($bgImg)."');" : 'background:var(--c-secondary);' ?>">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9 hero-content">
        <?php if (!empty($d['heading'])): ?><h1 class="hero-title"><?= e($d['heading']) ?></h1><?php endif; ?>
        <?php if (!empty($d['subheading'])): ?><p class="hero-sub"><?= e($d['subheading']) ?></p><?php endif; ?>
        <div class="hero-actions">
          <?php if (!empty($d['button_text'])): ?>
            <a href="<?= e($d['button_link'] ?? '#') ?>" class="btn btn-theme btn-lg"><?= e($d['button_text']) ?></a>
          <?php endif; ?>
          <?php if (!empty($d['button2_text'])): ?>
            <a href="<?= e($d['button2_link'] ?? '#') ?>" class="btn btn-outline-light btn-lg"><?= e($d['button2_text']) ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

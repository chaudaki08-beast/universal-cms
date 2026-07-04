<?php
$d = $data;
$cols = (int) ($d['columns'] ?? 3);
$colCls = ['2'=>'col-md-6','3'=>'col-md-4','4'=>'col-md-3'][$cols] ?? 'col-md-4';
$items = $d['items'] ?? [];
?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row g-4">
    <?php foreach ($items as $it): ?>
      <div class="<?= $colCls ?>">
        <div class="feature-card h-100">
          <?php if (!empty($it['image'])): ?>
            <img src="<?= e(uploads_url($it['image'])) ?>" alt="<?= e($it['title'] ?? '') ?>" class="feature-img">
          <?php elseif (!empty($it['icon'])): ?>
            <div class="feature-icon"><i class="fa <?= e($it['icon']) ?>"></i></div>
          <?php endif; ?>
          <h4 class="feature-title"><?= e($it['title'] ?? '') ?></h4>
          <p class="feature-text"><?= e($it['text'] ?? '') ?></p>
          <?php if (!empty($it['link'])): ?><a href="<?= e($it['link']) ?>" class="feature-link">Learn more <i class="fa fa-arrow-right"></i></a><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

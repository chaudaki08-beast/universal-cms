<?php $d = $data; $items = $d['items'] ?? []; ?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row g-4 justify-content-center">
    <?php foreach ($items as $p): $feat = !empty($p['featured']); ?>
      <div class="col-md-6 col-lg-4">
        <div class="price-card h-100 <?= $feat ? 'price-featured' : '' ?>">
          <?php if ($feat): ?><span class="price-badge">Popular</span><?php endif; ?>
          <h4 class="price-name"><?= e($p['name'] ?? '') ?></h4>
          <div class="price-amount"><?= money($p['price'] ?? 0) ?><span class="period"><?= e($p['period'] ?? '') ?></span></div>
          <ul class="price-features">
            <?php foreach (preg_split('/\r?\n/', (string)($p['features'] ?? '')) as $f): if (trim($f)==='') continue; ?>
              <li><i class="fa fa-check"></i> <?= e(trim($f)) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php if (!empty($p['button_text'])): ?>
            <a href="<?= e($p['button_link'] ?? '#') ?>" class="btn btn-theme w-100"><?= e($p['button_text']) ?></a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

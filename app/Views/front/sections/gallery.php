<?php $d = $data; $imgs = $d['images'] ?? []; ?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row g-3 gallery-grid">
    <?php foreach ($imgs as $img): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <a href="<?= e(uploads_url($img)) ?>" class="gallery-item" data-lightbox>
          <img src="<?= e(uploads_url($img)) ?>" alt="" loading="lazy">
        </a>
      </div>
    <?php endforeach; ?>
    <?php if (empty($imgs)): ?><p class="text-center text-muted">No images added yet.</p><?php endif; ?>
  </div>
</div>

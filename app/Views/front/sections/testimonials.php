<?php $d = $data; $items = $d['items'] ?? []; ?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row g-4 justify-content-center">
    <?php foreach ($items as $t): ?>
      <div class="col-md-6 col-lg-4">
        <div class="testimonial-card h-100">
          <div class="stars">
            <?php for ($i=0;$i<(int)($t['rating'] ?? 5);$i++): ?><i class="fa fa-star"></i><?php endfor; ?>
          </div>
          <p class="quote">“<?= e($t['quote'] ?? '') ?>”</p>
          <div class="author">
            <?php if (!empty($t['avatar'])): ?><img src="<?= e(uploads_url($t['avatar'])) ?>" class="avatar" alt=""><?php endif; ?>
            <div>
              <strong><?= e($t['name'] ?? '') ?></strong>
              <span class="role"><?= e($t['role'] ?? '') ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

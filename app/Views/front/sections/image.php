<?php $d = $data; $align = $d['align'] ?? 'center'; ?>
<div class="container text-<?= e($align) ?>">
  <?php if (!empty($d['image'])): ?>
    <figure class="single-image">
      <img src="<?= e(uploads_url($d['image'])) ?>" alt="<?= e($d['caption'] ?? '') ?>" class="img-fluid rounded">
      <?php if (!empty($d['caption'])): ?><figcaption><?= e($d['caption']) ?></figcaption><?php endif; ?>
    </figure>
  <?php endif; ?>
</div>

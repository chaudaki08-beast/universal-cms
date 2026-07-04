<?php $d = $data; ?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-4"><?= e($d['heading']) ?></h2><?php endif; ?>
  <?php if (!empty($d['address'])): ?><p class="text-center mb-4"><i class="fa fa-location-dot me-2"></i><?= e($d['address']) ?></p><?php endif; ?>
  <div class="map-embed ratio ratio-21x9 rounded overflow-hidden">
    <?php if (!empty($d['embed'])):
      // Only allow a Google Maps iframe src; otherwise treat as a plain URL.
      $embed = trim($d['embed']);
      if (preg_match('#^https://www\.google\.com/maps/embed#', $embed)): ?>
        <iframe src="<?= e($embed) ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
      <?php else: ?>
        <iframe src="https://maps.google.com/maps?q=<?= urlencode($d['address'] ?? $embed) ?>&output=embed" loading="lazy"></iframe>
      <?php endif; ?>
    <?php else: ?>
      <iframe src="https://maps.google.com/maps?q=<?= urlencode($d['address'] ?? 'world') ?>&output=embed" loading="lazy"></iframe>
    <?php endif; ?>
  </div>
</div>

<?php $d = $data; ?>
<div class="container">
  <div class="cta-box text-center">
    <?php if (!empty($d['heading'])): ?><h2 class="cta-title"><?= e($d['heading']) ?></h2><?php endif; ?>
    <?php if (!empty($d['subheading'])): ?><p class="cta-sub"><?= e($d['subheading']) ?></p><?php endif; ?>
    <?php if (!empty($d['button_text'])): ?>
      <a href="<?= e($d['button_link'] ?? '#') ?>" class="btn btn-light btn-lg"><?= e($d['button_text']) ?></a>
    <?php endif; ?>
  </div>
</div>

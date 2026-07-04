<?php $d = $data; ?>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-4"><?= e($d['heading']) ?></h2><?php endif; ?>
      <div class="rich-text"><?= $d['body'] ?? '' /* sanitized on save */ ?></div>
    </div>
  </div>
</div>

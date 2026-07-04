<?php
$imgs = json_field($product['images']);
$hasSale = $product['sale_price'] !== null && (float)$product['sale_price'] > 0;
?>
<div class="container section-pad">
  <div class="row g-5">
    <div class="col-lg-6">
      <?php if ($imgs): ?>
        <img src="<?= e(uploads_url($imgs[0])) ?>" class="img-fluid rounded mb-3" id="mainImg" alt="<?= e($product['name']) ?>">
        <?php if (count($imgs) > 1): ?>
          <div class="d-flex gap-2 flex-wrap">
            <?php foreach ($imgs as $im): ?>
              <img src="<?= e(uploads_url($im)) ?>" class="rounded" style="width:74px;height:74px;object-fit:cover;cursor:pointer"
                   onclick="document.getElementById('mainImg').src=this.src">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:380px"><i class="fa fa-image fa-3x text-muted"></i></div>
      <?php endif; ?>
    </div>
    <div class="col-lg-6">
      <?php if (!empty($product['category_name'])): ?><span class="badge bg-light text-dark mb-2"><?= e($product['category_name']) ?></span><?php endif; ?>
      <h1 class="mb-2"><?= e($product['name']) ?></h1>
      <div class="mb-3 fs-4">
        <?php if ($hasSale): ?>
          <span class="p-sale fw-bold"><?= money($product['sale_price']) ?></span><span class="p-old"><?= money($product['price']) ?></span>
        <?php else: ?>
          <span class="fw-bold" style="color:var(--c-secondary)"><?= money($product['price']) ?></span>
        <?php endif; ?>
      </div>
      <?php if (!empty($product['short_description'])): ?><p class="text-muted"><?= e($product['short_description']) ?></p><?php endif; ?>
      <p class="<?= (int)$product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
        <i class="fa fa-circle-check me-1"></i><?= (int)$product['stock'] > 0 ? 'In stock' : 'Out of stock' ?>
      </p>

      <?php if ((int)$product['stock'] > 0): ?>
      <form method="post" action="<?= base_url('cart/add') ?>" class="d-flex gap-2 align-items-center mb-4">
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <input type="number" name="qty" value="1" min="1" class="form-control" style="width:90px">
        <button class="btn btn-theme"><i class="fa fa-cart-plus me-1"></i>Add to Cart</button>
      </form>
      <?php endif; ?>

      <?php if (!empty($product['description'])): ?>
        <hr><h5 class="mt-4">Description</h5>
        <div class="rich-text"><?= nl2br(e($product['description'])) ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>

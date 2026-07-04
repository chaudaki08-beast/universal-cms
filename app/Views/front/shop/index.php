<div class="container section-pad">
  <h1 class="section-heading text-center mb-5"><?= e($activeCategory['name'] ?? 'Shop') ?></h1>
  <div class="row">
    <aside class="col-lg-3 mb-4">
      <h6 class="fw-bold mb-3">Categories</h6>
      <ul class="list-unstyled shop-cats">
        <li><a href="<?= base_url('shop') ?>" class="<?= empty($activeCategory)?'fw-bold':'' ?>">All Products</a></li>
        <?php foreach ($categories as $c): ?>
          <li><a href="<?= base_url('category/' . $c['slug']) ?>" class="<?= (!empty($activeCategory)&&$activeCategory['id']==$c['id'])?'fw-bold':'' ?>"><?= e($c['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <div class="col-lg-9">
      <div class="row g-4">
        <?php foreach ($products as $p):
          $imgs = json_field($p['images']); $img = $imgs[0] ?? '';
          $hasSale = $p['sale_price'] !== null && (float)$p['sale_price'] > 0; ?>
          <div class="col-sm-6 col-lg-4">
            <div class="product-card position-relative">
              <?php if ($hasSale): ?><span class="badge-sale">Sale</span><?php endif; ?>
              <a href="<?= base_url('product/' . $p['slug']) ?>">
                <?php if ($img): ?><img src="<?= e(uploads_url($img)) ?>" class="p-img" alt="<?= e($p['name']) ?>">
                <?php else: ?><div class="p-img bg-light d-flex align-items-center justify-content-center text-muted"><i class="fa fa-image fa-2x"></i></div><?php endif; ?>
              </a>
              <div class="p-body">
                <h6 class="mb-2"><a href="<?= base_url('product/' . $p['slug']) ?>" class="text-dark"><?= e($p['name']) ?></a></h6>
                <div>
                  <?php if ($hasSale): ?>
                    <span class="p-sale fw-bold"><?= money($p['sale_price']) ?></span><span class="p-old"><?= money($p['price']) ?></span>
                  <?php else: ?>
                    <span class="p-price"><?= money($p['price']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($products)): ?><p class="text-muted">No products found.</p><?php endif; ?>
      </div>

      <?php if (($totalPages ?? 1) > 1): ?>
        <nav class="mt-5"><ul class="pagination justify-content-center">
          <?php for ($i=1;$i<=$totalPages;$i++): ?>
            <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?p=<?= $i ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul></nav>
      <?php endif; ?>
    </div>
  </div>
</div>

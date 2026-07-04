<div class="page-head">
  <h2>Products</h2>
  <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New Product</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($products as $p): $imgs = json_field($p['images']); ?>
        <tr>
          <td class="fw-semibold d-flex align-items-center gap-2">
            <?php if (!empty($imgs[0])): ?><img src="<?= e(uploads_url($imgs[0])) ?>" style="width:38px;height:38px;object-fit:cover;border-radius:6px"><?php endif; ?>
            <?= e($p['name']) ?>
          </td>
          <td class="small text-muted"><?= e($p['sku'] ?? '—') ?></td>
          <td><?= money($p['price']) ?><?php if($p['sale_price']>0): ?> <span class="text-danger small">(<?= money($p['sale_price']) ?>)</span><?php endif; ?></td>
          <td><?= (int)$p['stock'] ?></td>
          <td><span class="badge-status badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td class="text-end">
            <a href="<?= base_url('admin/products/edit/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen"></i></a>
            <form method="post" action="<?= base_url('admin/products/destroy/'.$p['id']) ?>" class="d-inline" onsubmit="return confirm('Delete?')">
              <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($products)): ?><tr><td colspan="6" class="text-center text-muted py-4">No products yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>

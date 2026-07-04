<?php $p = $product; $imgs = $p ? json_field($p['images']) : []; ?>
<div class="page-head"><h2><?= $p ? 'Edit Product' : 'New Product' ?></h2><a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary">Back</a></div>

<form method="post" action="<?= base_url('admin/products/store') ?>">
  <?= csrf_field() ?>
  <?php if ($p): ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card"><div class="card-body">
        <div class="field-row"><label>Name</label><input name="name" id="prodName" data-slug-source="#prodSlug" class="form-control" value="<?= e($p['name'] ?? '') ?>" required></div>
        <div class="row">
          <div class="col-md-8 field-row"><label>Slug</label><input name="slug" id="prodSlug" class="form-control" value="<?= e($p['slug'] ?? '') ?>"></div>
          <div class="col-md-4 field-row"><label>SKU</label><input name="sku" class="form-control" value="<?= e($p['sku'] ?? '') ?>"></div>
        </div>
        <div class="field-row"><label>Short Description</label><textarea name="short_description" class="form-control" rows="2"><?= e($p['short_description'] ?? '') ?></textarea></div>
        <div class="field-row"><label>Full Description</label><textarea name="description" class="form-control" rows="6"><?= e($p['description'] ?? '') ?></textarea></div>
        <div class="field-row">
          <label>Product Images</label>
          <div id="prodImages" data-name="images[]">
            <?php foreach ($imgs as $img): ?>
              <div class="gallery-thumb position-relative" style="width:90px;height:70px;display:inline-block;margin:4px">
                <img src="<?= e(uploads_url($img)) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:8px">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 px-1 remove-gallery">&times;</button>
                <input type="hidden" name="images[]" value="<?= e($img) ?>">
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="openMediaPicker('prodImages','multi')"><i class="fa fa-plus me-1"></i>Add Images</button>
        </div>
      </div></div>
    </div>
    <div class="col-lg-4">
      <div class="card"><div class="card-body">
        <div class="row">
          <div class="col-6 field-row"><label>Price</label><input type="number" step="0.01" name="price" class="form-control" value="<?= e($p['price'] ?? '') ?>"></div>
          <div class="col-6 field-row"><label>Sale Price</label><input type="number" step="0.01" name="sale_price" class="form-control" value="<?= e($p['sale_price'] ?? '') ?>"></div>
          <div class="col-6 field-row"><label>Stock</label><input type="number" name="stock" class="form-control" value="<?= e($p['stock'] ?? 0) ?>"></div>
          <div class="col-6 field-row"><label>Status</label>
            <select name="status" class="form-select">
              <option value="draft" <?= ($p['status']??'')==='draft'?'selected':'' ?>>Draft</option>
              <option value="published" <?= ($p['status']??'')==='published'?'selected':'' ?>>Published</option>
            </select>
          </div>
        </div>
        <div class="field-row"><label>Category</label>
          <select name="category_id" class="form-select">
            <option value="">— none —</option>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= ($p['category_id']??null)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-check form-switch mb-3"><input type="checkbox" class="form-check-input" name="featured" value="1" <?= !empty($p['featured'])?'checked':'' ?>><label class="form-check-label">Featured product</label></div>
        <button class="btn btn-primary w-100"><i class="fa fa-save me-1"></i>Save Product</button>
      </div></div>
    </div>
  </div>
</form>

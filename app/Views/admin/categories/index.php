<div class="page-head"><h2>Categories</h2></div>

<ul class="nav nav-tabs settings-tabs mb-4">
  <li class="nav-item"><a class="nav-link <?= $type==='post'?'active':'' ?>" href="?type=post">Blog Categories</a></li>
  <li class="nav-item"><a class="nav-link <?= $type==='product'?'active':'' ?>" href="?type=product">Product Categories</a></li>
</ul>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card"><div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Name</th><th>Slug</th><th class="text-end"></th></tr></thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td class="fw-semibold"><?= e($c['name']) ?></td>
              <td class="text-muted"><?= e($c['slug']) ?></td>
              <td class="text-end">
                <form method="post" action="<?= base_url('admin/categories/destroy/'.$c['id']) ?>" onsubmit="return confirm('Delete?')">
                  <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($categories)): ?><tr><td colspan="3" class="text-muted text-center py-3">No categories yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-header bg-white fw-bold">Add Category</div><div class="card-body">
      <form method="post" action="<?= base_url('admin/categories/store') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="<?= e($type) ?>">
        <div class="field-row"><label>Name</label><input name="name" class="form-control" required></div>
        <div class="field-row"><label>Slug (optional)</label><input name="slug" class="form-control"></div>
        <div class="field-row"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <button class="btn btn-primary">Add Category</button>
      </form>
    </div></div>
  </div>
</div>

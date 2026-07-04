<div class="page-head"><h2>Menus</h2></div>

<ul class="nav nav-tabs settings-tabs mb-4">
  <?php foreach ($menus as $mn): ?>
    <li class="nav-item"><a class="nav-link <?= ($menu && $menu['id']==$mn['id'])?'active':'' ?>" href="?menu=<?= e($mn['slug']) ?>"><?= e($mn['name']) ?></a></li>
  <?php endforeach; ?>
</ul>

<?php if ($menu): ?>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card"><div class="card-header bg-white fw-bold">Items in “<?= e($menu['name']) ?>” <small class="text-muted fw-normal">— drag to reorder</small></div>
      <div class="card-body">
        <div id="menuItemList">
          <?php foreach ($items as $it): ?>
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-2" data-item-id="<?= $it['id'] ?>">
              <i class="fa fa-grip-vertical drag text-muted" style="cursor:move"></i>
              <div class="flex-grow-1">
                <strong><?= e($it['label']) ?></strong>
                <span class="text-muted small d-block"><?= e($it['url']) ?></span>
              </div>
              <form method="post" action="<?= base_url('admin/menus/deleteItem/'.$it['id']) ?>" onsubmit="return confirm('Remove?')">
                <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
              </form>
            </div>
          <?php endforeach; ?>
          <?php if (empty($items)): ?><p class="text-muted">No items yet.</p><?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card"><div class="card-header bg-white fw-bold">Add Menu Item</div><div class="card-body">
      <form method="post" action="<?= base_url('admin/menus/addItem') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
        <div class="field-row"><label>Label</label><input name="label" class="form-control" required></div>
        <div class="field-row"><label>URL</label><input name="url" class="form-control" value="/" required></div>
        <div class="field-row"><label>Or pick a page</label>
          <select class="form-select" onchange="if(this.value){this.closest('form').url.value=this.value}">
            <option value="">— select —</option>
            <option value="/">Home</option>
            <?php foreach ($pages as $pg): if($pg['slug']==='home')continue; ?>
              <option value="/<?= e($pg['slug']) ?>"><?= e($pg['title']) ?></option>
            <?php endforeach; ?>
            <option value="/blog">Blog</option>
            <option value="/shop">Shop</option>
          </select>
        </div>
        <div class="field-row"><label>Parent (for dropdown)</label>
          <select name="parent_id" class="form-select">
            <option value="">— top level —</option>
            <?php foreach ($items as $it): if($it['parent_id'])continue; ?><option value="<?= $it['id'] ?>"><?= e($it['label']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="field-row"><label>Open in</label>
          <select name="target" class="form-select"><option value="_self">Same tab</option><option value="_blank">New tab</option></select>
        </div>
        <button class="btn btn-primary">Add Item</button>
      </form>
    </div></div>
  </div>
</div>
<?php endif; ?>

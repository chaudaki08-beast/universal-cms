<div class="page-head"><h2>Create New Page</h2><a href="<?= base_url('admin/pages') ?>" class="btn btn-outline-secondary">Cancel</a></div>

<div class="card"><div class="card-body">
  <form method="post" action="<?= base_url('admin/pages/store') ?>" style="max-width:700px">
    <?= csrf_field() ?>
    <div class="field-row">
      <label>Page Title</label>
      <input name="title" id="pageTitle" data-slug-source="#pageSlug" class="form-control" required>
    </div>
    <div class="field-row">
      <label>URL Slug</label>
      <div class="input-group"><span class="input-group-text"><?= base_url('') ?></span><input name="slug" id="pageSlug" class="form-control"></div>
      <small class="text-muted">Leave blank to auto-generate from the title.</small>
    </div>
    <div class="field-row">
      <label>Start From Template</label>
      <select name="template_id" class="form-select">
        <option value="">Blank page</option>
        <?php foreach ($templates as $t): ?>
          <option value="<?= $t['id'] ?>"><?= e($t['name']) ?> — <?= e($t['description']) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="text-muted">Choosing a template pre-fills sections you can then edit.</small>
    </div>
    <div class="field-row">
      <label>Status</label>
      <select name="status" class="form-select" style="max-width:200px">
        <option value="draft">Draft</option><option value="published">Published</option>
      </select>
    </div>
    <button class="btn btn-primary">Create &amp; Build Page →</button>
  </form>
</div></div>

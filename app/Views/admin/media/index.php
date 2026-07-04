<div class="page-head">
  <h2>Media Library</h2>
</div>

<div class="card mb-4"><div class="card-body">
  <form method="post" action="<?= base_url('admin/media/upload') ?>" enctype="multipart/form-data" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <div class="col-md-5"><label class="form-label small fw-bold">Choose files (images / documents)</label><input type="file" name="files[]" class="form-control" multiple required></div>
    <div class="col-md-4"><label class="form-label small fw-bold">Folder</label><input name="folder" class="form-control" value="<?= e($folder ?: '/') ?>" placeholder="/ or /products"></div>
    <div class="col-md-3"><button class="btn btn-primary w-100"><i class="fa fa-upload me-1"></i>Upload</button></div>
  </form>
</div></div>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <form class="d-flex gap-2" method="get">
    <input name="q" class="form-control" placeholder="Search files…" value="<?= e($q) ?>" style="max-width:240px">
    <select name="folder" class="form-select" style="max-width:200px" onchange="this.form.submit()">
      <option value="">All folders</option>
      <?php foreach ($folders as $f): ?><option value="<?= e($f) ?>" <?= $folder===$f?'selected':'' ?>><?= e($f) ?></option><?php endforeach; ?>
    </select>
    <button class="btn btn-outline-secondary"><i class="fa fa-search"></i></button>
  </form>
  <span class="text-muted small"><?= count($items) ?> file(s)</span>
</div>

<div class="media-grid">
  <?php foreach ($items as $m): $isImg = strpos($m['mime'] ?? '', 'image') === 0; ?>
    <div class="media-tile">
      <?php if ($isImg): ?>
        <img src="<?= e(uploads_url($m['path'])) ?>" alt="<?= e($m['name']) ?>">
      <?php else: ?>
        <div class="d-flex align-items-center justify-content-center bg-light" style="height:120px"><i class="fa fa-file fa-2x text-muted"></i></div>
      <?php endif; ?>
      <div class="m-name" title="<?= e($m['name']) ?>"><?= e($m['name']) ?></div>
      <div class="m-actions">
        <button class="btn btn-sm btn-light" onclick="navigator.clipboard.writeText('<?= e(uploads_url($m['path'])) ?>');this.innerHTML='<i class=\'fa fa-check\'></i>'" title="Copy URL"><i class="fa fa-link"></i></button>
        <form method="post" action="<?= base_url('admin/media/destroy/'.$m['id']) ?>" onsubmit="return confirm('Delete this file?')">
          <?= csrf_field() ?><button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($items)): ?><p class="text-muted">No media found. Upload your first files above.</p><?php endif; ?>
</div>

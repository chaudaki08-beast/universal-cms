<div class="page-head">
  <h2>Pages</h2>
  <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New Page</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>Title</th><th>Slug</th><th>Template</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($pages as $p): ?>
          <tr>
            <td class="fw-semibold">
              <?php if ((int)$p['is_home']): ?><i class="fa fa-house text-warning me-1" title="Home page"></i><?php endif; ?>
              <?= e($p['title']) ?>
            </td>
            <td class="text-muted">/<?= e($p['slug']) ?></td>
            <td class="small"><?= e($p['template_name'] ?? '—') ?></td>
            <td><span class="badge-status badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <a href="<?= base_url(($p['slug']==='home'?'':$p['slug'])) ?>" target="_blank" class="btn btn-outline-secondary" title="View"><i class="fa fa-eye"></i></a>
                <a href="<?= base_url('admin/pages/edit/'.$p['id']) ?>" class="btn btn-outline-primary"><i class="fa fa-pen"></i> Edit</a>
                <form method="post" action="<?= base_url('admin/pages/duplicate/'.$p['id']) ?>" class="d-inline">
                  <?= csrf_field() ?><button class="btn btn-outline-secondary" title="Duplicate"><i class="fa fa-copy"></i></button>
                </form>
                <?php if (!(int)$p['is_home']): ?>
                <form method="post" action="<?= base_url('admin/pages/destroy/'.$p['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this page?')">
                  <?= csrf_field() ?><button class="btn btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($pages)): ?><tr><td colspan="5" class="text-center text-muted py-4">No pages yet. Create your first page.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

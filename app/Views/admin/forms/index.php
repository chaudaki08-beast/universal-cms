<div class="page-head">
  <h2>Forms</h2>
  <a href="<?= base_url('admin/forms/create') ?>" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New Form</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Form</th><th>Shortcode</th><th>Submissions</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($forms as $f): ?>
        <tr>
          <td class="fw-semibold"><?= e($f['name']) ?></td>
          <td><code>slug: <?= e($f['slug']) ?></code></td>
          <td><span class="badge bg-primary"><?= (int)$f['entry_count'] ?></span></td>
          <td class="text-end">
            <a href="<?= base_url('admin/forms/entries/'.$f['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="fa fa-inbox"></i> Entries</a>
            <a href="<?= base_url('admin/forms/edit/'.$f['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen"></i></a>
            <form method="post" action="<?= base_url('admin/forms/destroy/'.$f['id']) ?>" class="d-inline" onsubmit="return confirm('Delete?')">
              <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($forms)): ?><tr><td colspan="4" class="text-center text-muted py-4">No forms yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>

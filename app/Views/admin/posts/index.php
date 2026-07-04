<div class="page-head">
  <h2>Blog Posts</h2>
  <a href="<?= base_url('admin/posts/create') ?>" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New Post</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td class="fw-semibold"><?= e($p['title']) ?></td>
          <td class="small"><?= e($p['category_name'] ?? '—') ?></td>
          <td><span class="badge-status badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td class="small text-muted"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
          <td class="text-end">
            <a href="<?= base_url('admin/posts/edit/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen"></i></a>
            <form method="post" action="<?= base_url('admin/posts/destroy/'.$p['id']) ?>" class="d-inline" onsubmit="return confirm('Delete?')">
              <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($posts)): ?><tr><td colspan="5" class="text-center text-muted py-4">No posts yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>

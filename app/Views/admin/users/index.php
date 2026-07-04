<div class="page-head">
  <h2>Users &amp; Roles</h2>
  <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary"><i class="fa fa-plus me-1"></i>New User</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td class="fw-semibold"><?= e($u['name']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge bg-info text-dark"><?= e(ucwords(str_replace('_',' ',$u['role']))) ?></span></td>
          <td><?= (int)$u['is_active'] ? '<span class="text-success">Active</span>' : '<span class="text-muted">Disabled</span>' ?></td>
          <td class="small text-muted"><?= $u['last_login'] ? date('M j, Y', strtotime($u['last_login'])) : '—' ?></td>
          <td class="text-end">
            <a href="<?= base_url('admin/users/edit/'.$u['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-pen"></i></a>
            <form method="post" action="<?= base_url('admin/users/destroy/'.$u['id']) ?>" class="d-inline" onsubmit="return confirm('Delete user?')">
              <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div></div>

<div class="card mt-4"><div class="card-body">
  <h6 class="fw-bold">Role Capabilities</h6>
  <table class="table table-sm small mb-0">
    <tr><td><strong>Super Admin</strong></td><td>Full access — pages, e-commerce, users, settings, forms, everything.</td></tr>
    <tr><td><strong>Editor</strong></td><td>Manage pages, posts, products, media, menus; view form entries.</td></tr>
    <tr><td><strong>Content Manager</strong></td><td>Edit pages, manage posts &amp; media; view forms. No settings/users.</td></tr>
  </table>
</div></div>

<?php $u = $user; ?>
<div class="page-head"><h2><?= $u ? 'Edit User' : 'New User' ?></h2><a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">Back</a></div>
<div class="card"><div class="card-body" style="max-width:560px">
  <form method="post" action="<?= base_url('admin/users/store') ?>">
    <?= csrf_field() ?>
    <?php if ($u): ?><input type="hidden" name="id" value="<?= $u['id'] ?>"><?php endif; ?>
    <div class="field-row"><label>Full Name</label><input name="name" class="form-control" value="<?= e($u['name'] ?? '') ?>" required></div>
    <div class="field-row"><label>Email</label><input type="email" name="email" class="form-control" value="<?= e($u['email'] ?? '') ?>" required></div>
    <div class="field-row"><label>Role</label>
      <select name="role" class="form-select">
        <?php foreach (['super_admin'=>'Super Admin','editor'=>'Editor','content_manager'=>'Content Manager'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($u['role']??'')===$v?'selected':'' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field-row"><label>Password <?= $u ? '<small class="text-muted">(leave blank to keep current)</small>' : '' ?></label>
      <input type="password" name="password" class="form-control" <?= $u ? '' : 'required' ?> minlength="8"></div>
    <div class="form-check form-switch mb-3"><input type="checkbox" class="form-check-input" name="is_active" value="1" <?= (!$u || (int)$u['is_active']) ? 'checked' : '' ?>><label class="form-check-label">Account active</label></div>
    <button class="btn btn-primary"><i class="fa fa-save me-1"></i>Save User</button>
  </form>
</div></div>

<?php $f = $form; $fields = $f ? json_field($f['fields']) : []; ?>
<div class="page-head"><h2><?= $f ? 'Edit Form' : 'New Form' ?></h2><a href="<?= base_url('admin/forms') ?>" class="btn btn-outline-secondary">Back</a></div>

<form method="post" action="<?= base_url('admin/forms/store') ?>">
  <?= csrf_field() ?>
  <?php if ($f): ?><input type="hidden" name="id" value="<?= $f['id'] ?>"><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card"><div class="card-header bg-white fw-bold">Fields <small class="text-muted fw-normal">— add as many as you need</small></div><div class="card-body">
        <div class="repeater">
          <div class="repeater-items">
            <?php foreach ($fields as $i => $fld): ?>
              <div class="repeater-item">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
                <div class="row">
                  <div class="col-md-6 field-row"><label>Label</label><input name="field_label[]" class="form-control" value="<?= e($fld['label']) ?>"></div>
                  <div class="col-md-4 field-row"><label>Type</label>
                    <select name="field_type[]" class="form-select">
                      <?php foreach (['text','email','tel','number','textarea','date'] as $tp): ?><option value="<?= $tp ?>" <?= ($fld['type']??'')===$tp?'selected':'' ?>><?= ucfirst($tp) ?></option><?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-2 field-row"><label>Required</label><div class="form-check mt-2"><input type="checkbox" class="form-check-input" name="field_required[<?= $i ?>]" value="1" <?= !empty($fld['required'])?'checked':'' ?>></div></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <template>
            <div class="repeater-item">
              <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
              <div class="row">
                <div class="col-md-6 field-row"><label>Label</label><input name="field_label[]" class="form-control"></div>
                <div class="col-md-4 field-row"><label>Type</label>
                  <select name="field_type[]" class="form-select"><option>text</option><option>email</option><option>tel</option><option>number</option><option>textarea</option><option>date</option></select>
                </div>
                <div class="col-md-2 field-row"><label>Required</label><div class="form-check mt-2"><input type="checkbox" class="form-check-input" name="field_required[__i__]" value="1"></div></div>
              </div>
            </div>
          </template>
          <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fa fa-plus me-1"></i>Add Field</button>
        </div>
      </div></div>
    </div>
    <div class="col-lg-5">
      <div class="card"><div class="card-body">
        <div class="field-row"><label>Form Name</label><input name="name" id="formName" data-slug-source="#formSlug" class="form-control" value="<?= e($f['name'] ?? '') ?>" required></div>
        <div class="field-row"><label>Slug</label><input name="slug" id="formSlug" class="form-control" value="<?= e($f['slug'] ?? '') ?>">
          <small class="text-muted">Reference this slug in a Contact section.</small></div>
        <div class="field-row"><label>Notification Email</label><input type="email" name="notify_email" class="form-control" value="<?= e($f['notify_email'] ?? '') ?>" placeholder="leave blank to use site email"></div>
        <div class="field-row"><label>Success Message</label><textarea name="success_message" class="form-control" rows="2"><?= e($f['success_message'] ?? 'Thank you!') ?></textarea></div>
        <button class="btn btn-primary w-100"><i class="fa fa-save me-1"></i>Save Form</button>
      </div></div>
    </div>
  </div>
</form>

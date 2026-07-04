<?php $d = $data; $forms = $forms ?? []; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
<div class="field-row"><label>Form</label>
  <select name="data[form_slug]" class="form-select">
    <?php foreach ($forms as $f): ?>
      <option value="<?= e($f['slug']) ?>" <?= ($d['form_slug']??'contact')===$f['slug']?'selected':'' ?>><?= e($f['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <small class="text-muted">Manage forms under Forms in the sidebar.</small>
</div>
<div class="form-check form-switch">
  <input type="checkbox" class="form-check-input" name="data[show_info]" value="1" <?= !empty($d['show_info'])?'checked':'' ?>>
  <label class="form-check-label">Show contact info (address/phone/email from Settings) beside the form</label>
</div>

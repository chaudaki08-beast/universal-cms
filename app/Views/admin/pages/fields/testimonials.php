<?php $d = $data; $sid = $section['id']; $items = $d['items'] ?? []; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>

<label class="fw-bold">Testimonials</label>
<div class="repeater">
  <div class="repeater-items">
    <?php foreach ($items as $i => $it): ?>
      <div class="repeater-item">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
        <div class="row">
          <div class="col-md-6 field-row"><label>Name</label><input name="data[items][<?= $i ?>][name]" class="form-control" value="<?= e($it['name'] ?? '') ?>"></div>
          <div class="col-md-4 field-row"><label>Role</label><input name="data[items][<?= $i ?>][role]" class="form-control" value="<?= e($it['role'] ?? '') ?>"></div>
          <div class="col-md-2 field-row"><label>Rating</label><input type="number" min="1" max="5" name="data[items][<?= $i ?>][rating]" class="form-control" value="<?= e($it['rating'] ?? 5) ?>"></div>
          <div class="col-12 field-row"><label>Quote</label><textarea name="data[items][<?= $i ?>][quote]" class="form-control" rows="2"><?= e($it['quote'] ?? '') ?></textarea></div>
          <div class="col-md-6 field-row"><label>Avatar</label>
            <div class="media-input-group">
              <input type="text" id="tst_<?= $sid ?>_<?= $i ?>" name="data[items][<?= $i ?>][avatar]" class="form-control" value="<?= e($it['avatar'] ?? '') ?>" readonly>
              <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('tst_<?= $sid ?>_<?= $i ?>','single')"><i class="fa fa-image"></i></button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <template>
    <div class="repeater-item">
      <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
      <div class="row">
        <div class="col-md-6 field-row"><label>Name</label><input name="data[items][__i__][name]" class="form-control"></div>
        <div class="col-md-4 field-row"><label>Role</label><input name="data[items][__i__][role]" class="form-control"></div>
        <div class="col-md-2 field-row"><label>Rating</label><input type="number" min="1" max="5" name="data[items][__i__][rating]" class="form-control" value="5"></div>
        <div class="col-12 field-row"><label>Quote</label><textarea name="data[items][__i__][quote]" class="form-control" rows="2"></textarea></div>
      </div>
    </div>
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fa fa-plus me-1"></i>Add Testimonial</button>
</div>

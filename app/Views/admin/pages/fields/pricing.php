<?php $d = $data; $items = $d['items'] ?? []; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>

<label class="fw-bold">Plans</label>
<div class="repeater">
  <div class="repeater-items">
    <?php foreach ($items as $i => $it): ?>
      <div class="repeater-item">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
        <div class="row">
          <div class="col-md-5 field-row"><label>Plan Name</label><input name="data[items][<?= $i ?>][name]" class="form-control" value="<?= e($it['name'] ?? '') ?>"></div>
          <div class="col-md-4 field-row"><label>Price</label><input name="data[items][<?= $i ?>][price]" class="form-control" value="<?= e($it['price'] ?? '') ?>"></div>
          <div class="col-md-3 field-row"><label>Period</label><input name="data[items][<?= $i ?>][period]" class="form-control" value="<?= e($it['period'] ?? '/mo') ?>"></div>
          <div class="col-12 field-row"><label>Features (one per line)</label><textarea name="data[items][<?= $i ?>][features]" class="form-control" rows="3"><?= e($it['features'] ?? '') ?></textarea></div>
          <div class="col-md-6 field-row"><label>Button Text</label><input name="data[items][<?= $i ?>][button_text]" class="form-control" value="<?= e($it['button_text'] ?? '') ?>"></div>
          <div class="col-md-6 field-row"><label>Button Link</label><input name="data[items][<?= $i ?>][button_link]" class="form-control" value="<?= e($it['button_link'] ?? '') ?>"></div>
          <div class="col-12"><div class="form-check"><input type="checkbox" class="form-check-input" name="data[items][<?= $i ?>][featured]" value="1" <?= !empty($it['featured'])?'checked':'' ?>><label class="form-check-label">Highlight as featured plan</label></div></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <template>
    <div class="repeater-item">
      <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
      <div class="row">
        <div class="col-md-5 field-row"><label>Plan Name</label><input name="data[items][__i__][name]" class="form-control"></div>
        <div class="col-md-4 field-row"><label>Price</label><input name="data[items][__i__][price]" class="form-control"></div>
        <div class="col-md-3 field-row"><label>Period</label><input name="data[items][__i__][period]" class="form-control" value="/mo"></div>
        <div class="col-12 field-row"><label>Features (one per line)</label><textarea name="data[items][__i__][features]" class="form-control" rows="3"></textarea></div>
        <div class="col-md-6 field-row"><label>Button Text</label><input name="data[items][__i__][button_text]" class="form-control" value="Choose"></div>
        <div class="col-md-6 field-row"><label>Button Link</label><input name="data[items][__i__][button_link]" class="form-control" value="#"></div>
      </div>
    </div>
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fa fa-plus me-1"></i>Add Plan</button>
</div>

<?php $d = $data; $sid = $section['id']; $items = $d['items'] ?? []; ?>
<div class="row">
  <div class="col-md-8 field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
  <div class="col-md-4 field-row"><label>Columns</label>
    <select name="data[columns]" class="form-select">
      <?php foreach ([2,3,4] as $c): ?><option value="<?= $c ?>" <?= (int)($d['columns']??3)===$c?'selected':'' ?>><?= $c ?> per row</option><?php endforeach; ?>
    </select>
  </div>
</div>

<label class="fw-bold mt-2">Cards</label>
<div class="repeater" data-field="items">
  <div class="repeater-items">
    <?php foreach ($items as $i => $it): ?>
      <div class="repeater-item">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
        <div class="row">
          <div class="col-md-6 field-row"><label>Title</label><input name="data[items][<?= $i ?>][title]" class="form-control" value="<?= e($it['title'] ?? '') ?>"></div>
          <div class="col-md-6 field-row"><label>Icon (Font Awesome, e.g. fa-star)</label><input name="data[items][<?= $i ?>][icon]" class="form-control" value="<?= e($it['icon'] ?? '') ?>"></div>
          <div class="col-12 field-row"><label>Text</label><textarea name="data[items][<?= $i ?>][text]" class="form-control" rows="2"><?= e($it['text'] ?? '') ?></textarea></div>
          <div class="col-md-6 field-row">
            <label>Image (optional, overrides icon)</label>
            <div class="media-input-group">
              <input type="text" id="card_<?= $sid ?>_<?= $i ?>" name="data[items][<?= $i ?>][image]" class="form-control" value="<?= e($it['image'] ?? '') ?>" readonly>
              <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('card_<?= $sid ?>_<?= $i ?>','single')"><i class="fa fa-image"></i></button>
            </div>
          </div>
          <div class="col-md-6 field-row"><label>Link (optional)</label><input name="data[items][<?= $i ?>][link]" class="form-control" value="<?= e($it['link'] ?? '') ?>"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <template>
    <div class="repeater-item">
      <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
      <div class="row">
        <div class="col-md-6 field-row"><label>Title</label><input name="data[items][__i__][title]" class="form-control"></div>
        <div class="col-md-6 field-row"><label>Icon</label><input name="data[items][__i__][icon]" class="form-control" placeholder="fa-star"></div>
        <div class="col-12 field-row"><label>Text</label><textarea name="data[items][__i__][text]" class="form-control" rows="2"></textarea></div>
        <div class="col-md-6 field-row"><label>Link</label><input name="data[items][__i__][link]" class="form-control"></div>
      </div>
    </div>
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fa fa-plus me-1"></i>Add Card</button>
</div>

<?php $d = $data; $sid = $section['id']; ?>
<div class="field-row">
  <label>Image</label>
  <div class="media-input-group">
    <img id="img_<?= $sid ?>_preview" class="media-preview" src="<?= !empty($d['image']) ? e(uploads_url($d['image'])) : '' ?>" style="<?= empty($d['image'])?'display:none':'' ?>">
    <input type="text" id="img_<?= $sid ?>" name="data[image]" class="form-control" value="<?= e($d['image'] ?? '') ?>" readonly>
    <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('img_<?= $sid ?>','single')">Choose</button>
  </div>
</div>
<div class="field-row"><label>Caption</label><input name="data[caption]" class="form-control" value="<?= e($d['caption'] ?? '') ?>"></div>
<div class="field-row"><label>Alignment</label>
  <select name="data[align]" class="form-select" style="max-width:200px">
    <?php foreach (['left','center','right'] as $a): ?><option value="<?= $a ?>" <?= ($d['align']??'center')===$a?'selected':'' ?>><?= ucfirst($a) ?></option><?php endforeach; ?>
  </select>
</div>

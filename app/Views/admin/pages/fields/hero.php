<?php $d = $data; $sid = $section['id']; ?>
<div class="row">
  <div class="col-12 field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
  <div class="col-12 field-row"><label>Subheading</label><textarea name="data[subheading]" class="form-control" rows="2"><?= e($d['subheading'] ?? '') ?></textarea></div>

  <div class="col-md-6 field-row"><label>Button Text</label><input name="data[button_text]" class="form-control" value="<?= e($d['button_text'] ?? '') ?>"></div>
  <div class="col-md-6 field-row"><label>Button Link</label><input name="data[button_link]" class="form-control" value="<?= e($d['button_link'] ?? '') ?>"></div>
  <div class="col-md-6 field-row"><label>Secondary Button Text</label><input name="data[button2_text]" class="form-control" value="<?= e($d['button2_text'] ?? '') ?>"></div>
  <div class="col-md-6 field-row"><label>Secondary Button Link</label><input name="data[button2_link]" class="form-control" value="<?= e($d['button2_link'] ?? '') ?>"></div>

  <div class="col-12 field-row">
    <label>Background Image</label>
    <div class="media-input-group">
      <img id="hero_img_<?= $sid ?>_preview" class="media-preview" src="<?= !empty($d['background_image']) ? e(uploads_url($d['background_image'])) : '' ?>" style="<?= empty($d['background_image'])?'display:none':'' ?>">
      <input type="text" id="hero_img_<?= $sid ?>" name="data[background_image]" class="form-control" value="<?= e($d['background_image'] ?? '') ?>" readonly>
      <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('hero_img_<?= $sid ?>','single')">Choose</button>
    </div>
  </div>

  <div class="col-md-4 field-row"><label>Overlay Color</label><input name="data[overlay_color]" class="form-control" value="<?= e($d['overlay_color'] ?? 'rgba(15,23,42,0.55)') ?>"></div>
  <div class="col-md-4 field-row"><label>Text Alignment</label>
    <select name="data[align]" class="form-select">
      <?php foreach (['left','center','right'] as $a): ?><option value="<?= $a ?>" <?= ($d['align']??'center')===$a?'selected':'' ?>><?= ucfirst($a) ?></option><?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4 field-row"><label>Height</label>
    <select name="data[height]" class="form-select">
      <?php foreach (['small','medium','large'] as $h): ?><option value="<?= $h ?>" <?= ($d['height']??'large')===$h?'selected':'' ?>><?= ucfirst($h) ?></option><?php endforeach; ?>
    </select>
  </div>
</div>

<?php $d = $data; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
<div class="field-row"><label>Subheading</label><input name="data[subheading]" class="form-control" value="<?= e($d['subheading'] ?? '') ?>"></div>
<div class="row">
  <div class="col-md-6 field-row"><label>Button Text</label><input name="data[button_text]" class="form-control" value="<?= e($d['button_text'] ?? '') ?>"></div>
  <div class="col-md-6 field-row"><label>Button Link</label><input name="data[button_link]" class="form-control" value="<?= e($d['button_link'] ?? '') ?>"></div>
</div>

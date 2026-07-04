<?php $d = $data; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
<div class="field-row"><label>Address</label><input name="data[address]" class="form-control" value="<?= e($d['address'] ?? '') ?>" placeholder="123 Main St, City"></div>
<div class="field-row"><label>Google Maps Embed URL (optional)</label>
  <input name="data[embed]" class="form-control" value="<?= e($d['embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?...">
  <small class="text-muted">Leave blank to auto-generate a map from the address.</small>
</div>

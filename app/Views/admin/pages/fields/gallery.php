<?php $d = $data; $sid = $section['id']; $imgs = $d['images'] ?? []; $cid = 'gallery_' . $sid; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
<div class="field-row">
  <label>Images</label>
  <div id="<?= $cid ?>" data-name="data[images][]">
    <?php foreach ($imgs as $img): ?>
      <div class="gallery-thumb position-relative" style="width:90px;height:70px;display:inline-block;margin:4px">
        <img src="<?= e(uploads_url($img)) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:8px">
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 px-1 remove-gallery">&times;</button>
        <input type="hidden" name="data[images][]" value="<?= e($img) ?>">
      </div>
    <?php endforeach; ?>
  </div>
  <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="openMediaPicker('<?= $cid ?>','multi')"><i class="fa fa-plus me-1"></i>Add Images</button>
</div>

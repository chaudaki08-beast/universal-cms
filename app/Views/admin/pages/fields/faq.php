<?php $d = $data; $items = $d['items'] ?? []; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>

<label class="fw-bold">Questions</label>
<div class="repeater">
  <div class="repeater-items">
    <?php foreach ($items as $i => $it): ?>
      <div class="repeater-item">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
        <div class="field-row"><label>Question</label><input name="data[items][<?= $i ?>][q]" class="form-control" value="<?= e($it['q'] ?? '') ?>"></div>
        <div class="field-row"><label>Answer</label><textarea name="data[items][<?= $i ?>][a]" class="form-control" rows="2"><?= e($it['a'] ?? '') ?></textarea></div>
      </div>
    <?php endforeach; ?>
  </div>
  <template>
    <div class="repeater-item">
      <button type="button" class="btn btn-sm btn-outline-danger remove-item">&times;</button>
      <div class="field-row"><label>Question</label><input name="data[items][__i__][q]" class="form-control"></div>
      <div class="field-row"><label>Answer</label><textarea name="data[items][__i__][a]" class="form-control" rows="2"></textarea></div>
    </div>
  </template>
  <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add><i class="fa fa-plus me-1"></i>Add Question</button>
</div>

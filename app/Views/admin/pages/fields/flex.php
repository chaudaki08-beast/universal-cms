<?php
/**
 * Flexible Layout — drag & drop block builder (editor scaffold).
 * The interactive DOM is built by admin.js from the JSON below, so the
 * markup here stays minimal. $data has 'heading' + 'builder' (JSON string).
 */
$d = $data;
$builderJson = $d['builder'] ?? '{"rows":[]}';
// Re-encode safely for embedding inside the page.
$decoded = json_decode($builderJson, true);
$safeJson = json_encode(is_array($decoded) ? $decoded : ['rows' => []],
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES);
?>
<div class="flexbuilder" data-builder>
  <input type="hidden" name="data[builder]" class="flex-json" value="">
  <script type="application/json" class="flex-src"><?= $safeJson ?></script>

  <div class="field-row">
    <label>Section Heading (optional)</label>
    <input name="data[heading]" class="form-control flex-heading" value="<?= e($d['heading'] ?? '') ?>" placeholder="Leave blank for no heading">
  </div>

  <div class="flex-toolbar">
    <div class="flex-palette" title="Drag a block into a column — or click to add">
      <?php
      $blocks = [
        'heading' => ['fa-heading','Heading'], 'text' => ['fa-align-left','Text'],
        'image' => ['fa-image','Image'], 'button' => ['fa-hand-pointer','Button'],
        'icon' => ['fa-star','Icon Box'], 'video' => ['fa-video','Video'],
        'divider' => ['fa-minus','Divider'], 'spacer' => ['fa-arrows-up-down','Spacer'],
        'html' => ['fa-code','HTML'],
      ];
      foreach ($blocks as $type => $b): ?>
        <span class="palette-item" data-block="<?= $type ?>" draggable="true"><i class="fa <?= $b[0] ?> me-1"></i><?= $b[1] ?></span>
      <?php endforeach; ?>
    </div>
    <div class="dropdown flex-addrow">
      <button class="btn btn-sm btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fa fa-table-columns me-1"></i>Add Row</button>
      <ul class="dropdown-menu dropdown-menu-end">
        <?php foreach (['12'=>'1 Column','6-6'=>'2 Columns (½ ½)','4-4-4'=>'3 Columns','3-3-3-3'=>'4 Columns','8-4'=>'2 Cols (⅔ ⅓)','4-8'=>'2 Cols (⅓ ⅔)','3-6-3'=>'3 Cols (¼ ½ ¼)','9-3'=>'2 Cols (¾ ¼)'] as $lay=>$lbl): ?>
          <li><button class="dropdown-item add-row" type="button" data-layout="<?= $lay ?>"><?= $lbl ?></button></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="flex-rows"></div>
  <div class="flex-empty text-center text-muted py-4">
    <i class="fa fa-table-cells-large fa-2x mb-2 d-block"></i>
    Add a row, then drag blocks into the columns.
  </div>
</div>

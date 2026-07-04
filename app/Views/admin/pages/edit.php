<?php
/** @var array $page @var array $sections @var array $sectionTypes @var array $forms */
$liveUrl = base_url($page['slug'] === 'home' ? '' : $page['slug']);
?>
<div class="page-head">
  <div>
    <a href="<?= base_url('admin/pages') ?>" class="text-muted small text-decoration-none"><i class="fa fa-arrow-left me-1"></i>All Pages</a>
    <h2 class="mt-1"><?= e($page['title']) ?></h2>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= $liveUrl ?>" target="_blank" class="btn btn-outline-secondary"><i class="fa fa-eye me-1"></i>Preview</a>
  </div>
</div>

<div class="row g-4">
  <!-- LEFT: section builder -->
  <div class="col-lg-8">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Page Sections</h5>
      <div class="dropdown">
        <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-plus me-1"></i>Add Section</button>
        <ul class="dropdown-menu dropdown-menu-end" style="max-height:340px;overflow:auto">
          <?php foreach ($sectionTypes as $type => $label): ?>
            <li>
              <form method="post" action="<?= base_url('admin/pages/addSection/'.$page['id']) ?>">
                <?= csrf_field() ?><input type="hidden" name="type" value="<?= $type ?>">
                <button class="dropdown-item" type="submit"><?= e($label) ?></button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div id="sectionList">
      <?php foreach ($sections as $section):
        $data = json_field($section['data']);
        $set  = json_field($section['settings']);
        ?>
        <div class="builder-section" id="section-<?= $section['id'] ?>" data-section-id="<?= $section['id'] ?>">
          <div class="builder-head">
            <i class="fa fa-grip-vertical drag"></i>
            <span class="type-badge"><?= e($section['type']) ?></span>
            <span class="sec-title"><?= e($section['title']) ?></span>
            <?php if (!(int)$section['is_visible']): ?><span class="badge bg-secondary">Hidden</span><?php endif; ?>
            <span class="actions">
              <form method="post" action="<?= base_url('admin/pages/deleteSection/'.$section['id']) ?>" onsubmit="return confirm('Remove this section?')">
                <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>
              </form>
            </span>
          </div>
          <div class="builder-body">
            <form method="post" action="<?= base_url('admin/pages/saveSection/'.$section['id']) ?>">
              <?= csrf_field() ?>
              <div class="row">
                <div class="col-md-8 field-row">
                  <label>Section Label (admin only)</label>
                  <input name="title" class="form-control" value="<?= e($section['title']) ?>">
                </div>
                <div class="col-md-4 field-row">
                  <label>Visibility</label>
                  <div class="form-check form-switch mt-2">
                    <input type="checkbox" class="form-check-input" name="is_visible" value="1" <?= (int)$section['is_visible'] ? 'checked' : '' ?>>
                    <label class="form-check-label">Visible on site</label>
                  </div>
                </div>
              </div>
              <hr>
              <?php
                $type = $section['type'];
                require APP_PATH . '/Views/admin/pages/fields/' . preg_replace('/[^a-z]/', '', $type) . '.php';
              ?>
              <hr>
              <?php $sid = $section['id']; $padOpts = ['none'=>'None','sm'=>'Small','md'=>'Medium','lg'=>'Large','xl'=>'X-Large']; ?>
              <div class="design-panel">
                <button class="design-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#design-<?= $sid ?>">
                  <i class="fa fa-sliders me-1"></i>Design &amp; Layout <i class="fa fa-chevron-down ms-1 small"></i>
                </button>
                <div class="collapse" id="design-<?= $sid ?>">
                  <div class="row gx-3 mt-2">
                    <div class="col-md-4 field-row">
                      <label>Content Width</label>
                      <select name="settings[width]" class="form-select form-select-sm">
                        <?php foreach (['boxed'=>'Boxed (default)','narrow'=>'Narrow','full'=>'Full width'] as $v=>$l): ?>
                          <option value="<?= $v ?>" <?= ($set['width'] ?? 'boxed')===$v?'selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 field-row">
                      <label>Text Alignment</label>
                      <select name="settings[align]" class="form-select form-select-sm">
                        <?php foreach (['inherit'=>'Default','left'=>'Left','center'=>'Center','right'=>'Right'] as $v=>$l): ?>
                          <option value="<?= $v ?>" <?= ($set['align'] ?? 'inherit')===$v?'selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 field-row">
                      <label>Text Color</label>
                      <select name="settings[text_theme]" class="form-select form-select-sm">
                        <?php foreach (['auto'=>'Auto','dark'=>'Dark text','light'=>'Light text'] as $v=>$l): ?>
                          <option value="<?= $v ?>" <?= ($set['text_theme'] ?? 'auto')===$v?'selected':'' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-3 field-row">
                      <label>Padding Top</label>
                      <select name="settings[pad_top]" class="form-select form-select-sm">
                        <?php foreach ($padOpts as $v=>$l): ?><option value="<?= $v ?>" <?= ($set['pad_top'] ?? $set['padding'] ?? 'lg')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-3 field-row">
                      <label>Padding Bottom</label>
                      <select name="settings[pad_bottom]" class="form-select form-select-sm">
                        <?php foreach ($padOpts as $v=>$l): ?><option value="<?= $v ?>" <?= ($set['pad_bottom'] ?? $set['padding'] ?? 'lg')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6 field-row">
                      <label>Background Color</label>
                      <div class="color-field">
                        <input type="color" value="<?= e($set['bg'] ?? '#ffffff') ?>" oninput="this.nextElementSibling.value=this.value">
                        <input name="settings[bg]" class="form-control form-control-sm" value="<?= e($set['bg'] ?? '') ?>" placeholder="blank = none">
                      </div>
                    </div>
                    <div class="col-md-6 field-row">
                      <label>Background Image</label>
                      <div class="media-input-group">
                        <input type="text" id="secbg_<?= $sid ?>" name="settings[bg_image]" class="form-control form-control-sm" value="<?= e($set['bg_image'] ?? '') ?>" readonly>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openMediaPicker('secbg_<?= $sid ?>','single')"><i class="fa fa-image"></i></button>
                        <?php if (!empty($set['bg_image'])): ?><button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('secbg_<?= $sid ?>').value=''"><i class="fa fa-times"></i></button><?php endif; ?>
                      </div>
                    </div>
                    <div class="col-md-6 field-row">
                      <label>Image Overlay (for readability)</label>
                      <input name="settings[bg_overlay]" class="form-control form-control-sm" value="<?= e($set['bg_overlay'] ?? '') ?>" placeholder="e.g. rgba(0,0,0,0.5)">
                    </div>
                    <div class="col-md-6 field-row">
                      <label>Custom CSS Class</label>
                      <input name="settings[css_class]" class="form-control form-control-sm" value="<?= e($set['css_class'] ?? '') ?>" placeholder="optional">
                    </div>
                  </div>
                </div>
              </div>
              <button class="btn btn-primary btn-sm mt-2"><i class="fa fa-save me-1"></i>Save Section</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($sections)): ?>
      <div class="card"><div class="card-body text-center text-muted py-5">
        <i class="fa fa-layer-group fa-2x mb-2"></i><p>No sections yet. Use “Add Section” to start building.</p>
      </div></div>
    <?php endif; ?>
  </div>

  <!-- RIGHT: page settings + SEO -->
  <div class="col-lg-4">
    <form method="post" action="<?= base_url('admin/pages/update/'.$page['id']) ?>">
      <?= csrf_field() ?>
      <div class="card mb-3"><div class="card-header bg-white fw-bold">Page Settings</div><div class="card-body">
        <div class="field-row"><label>Title</label><input name="title" class="form-control" value="<?= e($page['title']) ?>"></div>
        <div class="field-row"><label>Slug</label><input name="slug" class="form-control" value="<?= e($page['slug']) ?>"></div>
        <div class="field-row"><label>Status</label>
          <select name="status" class="form-select">
            <option value="draft" <?= $page['status']==='draft'?'selected':'' ?>>Draft</option>
            <option value="published" <?= $page['status']==='published'?'selected':'' ?>>Published</option>
          </select>
        </div>
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_home" value="1" <?= (int)$page['is_home']?'checked':'' ?>><label class="form-check-label">Set as Home page</label></div>
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="show_header" value="1" <?= (int)$page['show_header']?'checked':'' ?>><label class="form-check-label">Show header</label></div>
        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="show_footer" value="1" <?= (int)$page['show_footer']?'checked':'' ?>><label class="form-check-label">Show footer</label></div>
      </div></div>

      <div class="card mb-3"><div class="card-header bg-white fw-bold">SEO</div><div class="card-body">
        <div class="field-row"><label>Meta Title</label><input name="meta_title" class="form-control" value="<?= e($page['meta_title']) ?>"></div>
        <div class="field-row"><label>Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?= e($page['meta_description']) ?></textarea></div>
        <div class="field-row"><label>Keywords</label><input name="meta_keywords" class="form-control" value="<?= e($page['meta_keywords']) ?>"></div>
        <div class="field-row"><label>OG Image</label>
          <div class="media-input-group">
            <input type="text" id="og_image" name="og_image" class="form-control" value="<?= e($page['og_image']) ?>" readonly>
            <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('og_image','single')"><i class="fa fa-image"></i></button>
          </div>
        </div>
        <div class="field-row"><label>Canonical URL</label><input name="canonical_url" class="form-control" value="<?= e($page['canonical_url']) ?>"></div>
      </div></div>

      <button class="btn btn-primary w-100"><i class="fa fa-save me-1"></i>Save Page Settings</button>
    </form>
  </div>
</div>

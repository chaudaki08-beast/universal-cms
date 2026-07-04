<?php
$g = $settings['general']; $t = $settings['theme']; $s = $settings['seo']; $so = $settings['social'];
$h = $settings['header'] ?? []; $f = $settings['footer'] ?? [];
$val = fn($arr,$k,$d='') => ($arr[$k] ?? '') !== '' ? $arr[$k] : $d;
?>
<div class="page-head"><h2>Settings</h2></div>

<ul class="nav nav-tabs settings-tabs mb-4">
  <?php foreach (['general'=>'General','theme'=>'Theme & Design','header'=>'Header','footer'=>'Footer','seo'=>'SEO','social'=>'Social'] as $k=>$lbl): ?>
    <li class="nav-item"><a class="nav-link <?= $tab===$k?'active':'' ?>" href="?tab=<?= $k ?>"><?= $lbl ?></a></li>
  <?php endforeach; ?>
</ul>

<div class="card"><div class="card-body" style="max-width:820px">

<?php if ($tab === 'general'): ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?><input type="hidden" name="group" value="general">
    <div class="field-row"><label>Website Name</label><input name="settings[site_name]" class="form-control" value="<?= e($val($g,'site_name')) ?>"></div>
    <div class="field-row"><label>Tagline</label><input name="settings[site_tagline]" class="form-control" value="<?= e($val($g,'site_tagline')) ?>"></div>
    <div class="row">
      <div class="col-md-6 field-row"><label>Logo</label>
        <?php if ($val($g,'logo')): ?><div><img src="<?= e(uploads_url($val($g,'logo'))) ?>" height="40" class="mb-2"></div><?php endif; ?>
        <input type="file" name="logo" class="form-control" accept="image/*">
      </div>
      <div class="col-md-6 field-row"><label>Favicon</label>
        <?php if ($val($g,'favicon')): ?><div><img src="<?= e(uploads_url($val($g,'favicon'))) ?>" height="28" class="mb-2"></div><?php endif; ?>
        <input type="file" name="favicon" class="form-control" accept="image/*">
      </div>
    </div>
    <hr><h6 class="fw-bold">Contact Information</h6>
    <div class="row">
      <div class="col-md-6 field-row"><label>Email</label><input name="settings[contact_email]" class="form-control" value="<?= e($val($g,'contact_email')) ?>"></div>
      <div class="col-md-6 field-row"><label>Phone</label><input name="settings[contact_phone]" class="form-control" value="<?= e($val($g,'contact_phone')) ?>"></div>
      <div class="col-12 field-row"><label>Address</label><input name="settings[contact_address]" class="form-control" value="<?= e($val($g,'contact_address')) ?>"></div>
      <div class="col-md-4 field-row"><label>Currency Symbol</label><input name="settings[currency_symbol]" class="form-control" value="<?= e($val($g,'currency_symbol','$')) ?>"></div>
    </div>
    <button class="btn btn-primary">Save General Settings</button>
  </form>

<?php elseif ($tab === 'theme'): ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>">
    <?= csrf_field() ?><input type="hidden" name="group" value="theme">
    <div class="row">
      <div class="col-md-4 field-row"><label>Primary Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($t,'primary_color','#2563eb')) ?>" oninput="this.nextElementSibling.value=this.value">
        <input name="settings[primary_color]" class="form-control" value="<?= e($val($t,'primary_color','#2563eb')) ?>"></div>
      </div>
      <div class="col-md-4 field-row"><label>Secondary Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($t,'secondary_color','#0f172a')) ?>" oninput="this.nextElementSibling.value=this.value">
        <input name="settings[secondary_color]" class="form-control" value="<?= e($val($t,'secondary_color','#0f172a')) ?>"></div>
      </div>
      <div class="col-md-4 field-row"><label>Accent Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($t,'accent_color','#f59e0b')) ?>" oninput="this.nextElementSibling.value=this.value">
        <input name="settings[accent_color]" class="form-control" value="<?= e($val($t,'accent_color','#f59e0b')) ?>"></div>
      </div>
      <div class="col-md-6 field-row"><label>Body Font (Google Font name)</label><input name="settings[body_font]" class="form-control" value="<?= e($val($t,'body_font','Inter')) ?>"></div>
      <div class="col-md-6 field-row"><label>Heading Font</label><input name="settings[heading_font]" class="form-control" value="<?= e($val($t,'heading_font','Poppins')) ?>"></div>
      <div class="col-md-4 field-row"><label>Border Radius (px)</label><input type="number" name="settings[border_radius]" class="form-control" value="<?= e($val($t,'border_radius','10')) ?>"></div>
      <div class="col-md-4 field-row"><label>Button Style</label>
        <select name="settings[button_style]" class="form-select">
          <?php foreach (['solid'=>'Solid','outline'=>'Outline','pill'=>'Pill'] as $v=>$l): ?><option value="<?= $v ?>" <?= $val($t,'button_style')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 field-row"><label>Layout Max Width (px)</label><input type="number" name="settings[layout_width]" class="form-control" value="<?= e($val($t,'layout_width','1200')) ?>"></div>
      <div class="col-md-6 field-row"><label>Header Layout</label>
        <select name="settings[header_layout]" class="form-select">
          <?php foreach (['classic'=>'Classic','centered'=>'Centered','minimal'=>'Minimal'] as $v=>$l): ?><option value="<?= $v ?>" <?= $val($t,'header_layout')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6 field-row"><label>Footer Layout</label>
        <select name="settings[footer_layout]" class="form-select">
          <?php foreach (['columns'=>'Columns','simple'=>'Simple','centered'=>'Centered'] as $v=>$l): ?><option value="<?= $v ?>" <?= $val($t,'footer_layout')===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-12"><div class="form-check form-switch"><input type="hidden" name="settings[sticky_header]" value="0"><input type="checkbox" class="form-check-input" name="settings[sticky_header]" value="1" <?= $val($t,'sticky_header')?'checked':'' ?>><label class="form-check-label">Sticky header</label></div></div>
    </div>
    <button class="btn btn-primary mt-3">Save Theme</button>
  </form>

<?php elseif ($tab === 'header'): ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>">
    <?= csrf_field() ?><input type="hidden" name="group" value="header">
    <div class="row">
      <div class="col-md-6 field-row"><label>Header Layout</label>
        <select name="settings[header_layout]" class="form-select">
          <?php foreach (['classic'=>'Classic — logo left, menu right','centered'=>'Centered — logo on top, menu below','split'=>'Split — menu around centered logo','minimal'=>'Minimal — clean, no top bar'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= $val($h,'header_layout','classic')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6 field-row"><label>Header Width</label>
        <select name="settings[header_container]" class="form-select">
          <option value="boxed" <?= $val($h,'header_container','boxed')==='boxed'?'selected':'' ?>>Boxed (matches layout width)</option>
          <option value="full" <?= $val($h,'header_container','boxed')==='full'?'selected':'' ?>>Full width</option>
        </select>
      </div>
      <div class="col-md-6 field-row"><label>CTA Button Text</label><input name="settings[header_cta_text]" class="form-control" value="<?= e($val($h,'header_cta_text')) ?>" placeholder="e.g. Book Now (blank = hide)"></div>
      <div class="col-md-6 field-row"><label>CTA Button Link</label><input name="settings[header_cta_link]" class="form-control" value="<?= e($val($h,'header_cta_link','#')) ?>"></div>
      <div class="col-md-6 field-row"><label>Header Background Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($h,'header_bg','#ffffff')) ?>" oninput="this.nextElementSibling.value=this.value"><input name="settings[header_bg]" class="form-control" value="<?= e($val($h,'header_bg')) ?>" placeholder="blank = default"></div>
      </div>
      <div class="col-md-6 field-row"><label>Header Text Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($h,'header_text','#0f172a')) ?>" oninput="this.nextElementSibling.value=this.value"><input name="settings[header_text]" class="form-control" value="<?= e($val($h,'header_text')) ?>" placeholder="blank = default"></div>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-4 mt-2">
      <div class="form-check form-switch"><input type="hidden" name="settings[header_show_topbar]" value="0"><input type="checkbox" class="form-check-input" name="settings[header_show_topbar]" value="1" <?= (int)$val($h,'header_show_topbar',1)?'checked':'' ?>><label class="form-check-label">Show top contact bar</label></div>
      <div class="form-check form-switch"><input type="hidden" name="settings[sticky_header]" value="0"><input type="checkbox" class="form-check-input" name="settings[sticky_header]" value="1" <?= (int)$val($h,'sticky_header', (int)($t['sticky_header']??1))?'checked':'' ?>><label class="form-check-label">Sticky header</label></div>
      <div class="form-check form-switch"><input type="hidden" name="settings[header_transparent]" value="0"><input type="checkbox" class="form-check-input" name="settings[header_transparent]" value="1" <?= (int)$val($h,'header_transparent',0)?'checked':'' ?>><label class="form-check-label">Transparent over hero</label></div>
    </div>
    <button class="btn btn-primary mt-3">Save Header</button>
  </form>

<?php elseif ($tab === 'footer'): ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>">
    <?= csrf_field() ?><input type="hidden" name="group" value="footer">
    <div class="row">
      <div class="col-md-6 field-row"><label>Footer Layout</label>
        <select name="settings[footer_layout]" class="form-select">
          <?php foreach (['columns'=>'Columns — full multi-column','compact'=>'Compact — brand + 2 columns','simple'=>'Simple — centered brand & links','minimal'=>'Minimal — single line'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= $val($f,'footer_layout','columns')===$v?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6 field-row"><label>About Text</label><input name="settings[footer_about]" class="form-control" value="<?= e($val($f,'footer_about')) ?>" placeholder="blank = use site tagline"></div>
      <div class="col-md-6 field-row"><label>Links Column Title</label><input name="settings[footer_col2_title]" class="form-control" value="<?= e($val($f,'footer_col2_title','Quick Links')) ?>"></div>
      <div class="col-md-6 field-row"><label>Contact Column Title</label><input name="settings[footer_col3_title]" class="form-control" value="<?= e($val($f,'footer_col3_title','Get in Touch')) ?>"></div>
      <div class="col-md-6 field-row"><label>Footer Background Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($f,'footer_bg','#0f172a')) ?>" oninput="this.nextElementSibling.value=this.value"><input name="settings[footer_bg]" class="form-control" value="<?= e($val($f,'footer_bg')) ?>" placeholder="blank = theme secondary"></div>
      </div>
      <div class="col-md-6 field-row"><label>Footer Text Color</label>
        <div class="color-field"><input type="color" value="<?= e($val($f,'footer_text','#cbd5e1')) ?>" oninput="this.nextElementSibling.value=this.value"><input name="settings[footer_text]" class="form-control" value="<?= e($val($f,'footer_text')) ?>" placeholder="blank = default"></div>
      </div>
      <div class="col-12 field-row"><label>Copyright Text</label>
        <input name="settings[footer_copyright]" class="form-control" value="<?= e($val($f,'footer_copyright')) ?>" placeholder="© {year} {site}. All rights reserved.">
        <small class="text-muted">Use <code>{year}</code> and <code>{site}</code> as placeholders.</small>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-4 mt-2">
      <div class="form-check form-switch"><input type="hidden" name="settings[footer_show_social]" value="0"><input type="checkbox" class="form-check-input" name="settings[footer_show_social]" value="1" <?= (int)$val($f,'footer_show_social',1)?'checked':'' ?>><label class="form-check-label">Show social icons</label></div>
      <div class="form-check form-switch"><input type="hidden" name="settings[footer_newsletter]" value="0"><input type="checkbox" class="form-check-input" name="settings[footer_newsletter]" value="1" <?= (int)$val($f,'footer_newsletter',0)?'checked':'' ?>><label class="form-check-label">Show newsletter signup (needs a form with slug “newsletter”)</label></div>
    </div>
    <button class="btn btn-primary mt-3">Save Footer</button>
  </form>

<?php elseif ($tab === 'seo'): ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?><input type="hidden" name="group" value="seo">
    <div class="field-row"><label>Default Meta Title</label><input name="settings[meta_title]" class="form-control" value="<?= e($val($s,'meta_title')) ?>"></div>
    <div class="field-row"><label>Default Meta Description</label><textarea name="settings[meta_description]" class="form-control" rows="3"><?= e($val($s,'meta_description')) ?></textarea></div>
    <div class="field-row"><label>Default Keywords</label><input name="settings[meta_keywords]" class="form-control" value="<?= e($val($s,'meta_keywords')) ?>"></div>
    <div class="field-row"><label>Default OG Image</label>
      <?php if ($val($s,'og_image')): ?><div><img src="<?= e(uploads_url($val($s,'og_image'))) ?>" height="60" class="mb-2"></div><?php endif; ?>
      <input type="file" name="og_image" class="form-control" accept="image/*">
    </div>
    <div class="alert alert-info small">Sitemap: <a href="<?= base_url('sitemap.xml') ?>" target="_blank"><?= base_url('sitemap.xml') ?></a> · Robots: <a href="<?= base_url('robots.txt') ?>" target="_blank"><?= base_url('robots.txt') ?></a></div>
    <button class="btn btn-primary">Save SEO</button>
  </form>

<?php else: ?>
  <form method="post" action="<?= base_url('admin/settings/save') ?>">
    <?= csrf_field() ?><input type="hidden" name="group" value="social">
    <?php foreach (['facebook'=>'Facebook','instagram'=>'Instagram','twitter'=>'Twitter / X','linkedin'=>'LinkedIn','youtube'=>'YouTube'] as $k=>$lbl): ?>
      <div class="field-row"><label><?= $lbl ?> URL</label><input name="settings[<?= $k ?>]" class="form-control" value="<?= e($val($so,$k)) ?>"></div>
    <?php endforeach; ?>
    <button class="btn btn-primary">Save Social Links</button>
  </form>
<?php endif; ?>

</div></div>

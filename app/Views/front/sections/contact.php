<?php
$d = $data;
$formSlug = $d['form_slug'] ?? 'contact';
$form = \App\Models\Form::bySlug($formSlug);
$fields = $form ? json_field($form['fields']) : [];
$showInfo = !empty($d['show_info']);
?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row g-5 justify-content-center">
    <?php if ($showInfo): ?>
    <div class="col-lg-4">
      <div class="contact-info">
        <?php if (!empty($contact['address'])): ?><p><i class="fa fa-location-dot"></i> <?= e($contact['address']) ?></p><?php endif; ?>
        <?php if (!empty($contact['phone'])): ?><p><i class="fa fa-phone"></i> <?= e($contact['phone']) ?></p><?php endif; ?>
        <?php if (!empty($contact['email'])): ?><p><i class="fa fa-envelope"></i> <?= e($contact['email']) ?></p><?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="col-lg-<?= $showInfo ? '7' : '8' ?>">
      <?php if ($form): ?>
        <form method="post" action="<?= base_url('form/submit') ?>" class="cms-form">
          <?= csrf_field() ?>
          <input type="hidden" name="form_slug" value="<?= e($formSlug) ?>">
          <input type="hidden" name="_redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">
          <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">
          <div class="row g-3">
            <?php foreach ($fields as $f): $req = !empty($f['required']) ? 'required' : ''; ?>
              <div class="col-<?= in_array($f['type'],['textarea']) ? '12' : 'md-6' ?>">
                <label class="form-label"><?= e($f['label']) ?><?= $req ? ' *' : '' ?></label>
                <?php if (($f['type'] ?? 'text') === 'textarea'): ?>
                  <textarea name="field_<?= e($f['name']) ?>" class="form-control" rows="4" <?= $req ?>></textarea>
                <?php else: ?>
                  <input type="<?= e($f['type'] ?? 'text') ?>" name="field_<?= e($f['name']) ?>" class="form-control" <?= $req ?>>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="submit" class="btn btn-theme mt-4">Send Message</button>
        </form>
      <?php else: ?>
        <p class="text-center text-muted">Form “<?= e($formSlug) ?>” not found. Create it in the admin Forms section.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php $d = $data; $items = $d['items'] ?? []; $uid = 'faq' . ($section['id'] ?? rand()); ?>
<div class="container">
  <?php if (!empty($d['heading'])): ?><h2 class="section-heading text-center mb-5"><?= e($d['heading']) ?></h2><?php endif; ?>
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="accordion faq-accordion" id="<?= $uid ?>">
        <?php foreach ($items as $i => $f): $iid = $uid . '_' . $i; ?>
          <div class="accordion-item">
            <h3 class="accordion-header">
              <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button"
                      data-bs-toggle="collapse" data-bs-target="#c<?= $iid ?>">
                <?= e($f['q'] ?? '') ?>
              </button>
            </h3>
            <div id="c<?= $iid ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#<?= $uid ?>">
              <div class="accordion-body"><?= e($f['a'] ?? '') ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

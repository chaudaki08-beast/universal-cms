<div class="page-head"><h2>Entries: <?= e($form['name']) ?></h2><a href="<?= base_url('admin/forms') ?>" class="btn btn-outline-secondary">Back</a></div>
<div class="card"><div class="card-body">
  <?php if (empty($entries)): ?>
    <p class="text-muted text-center py-4">No submissions yet.</p>
  <?php else: ?>
    <?php foreach ($entries as $en): $d = json_field($en['data']); ?>
      <div class="border rounded p-3 mb-3">
        <div class="d-flex justify-content-between mb-2">
          <span class="small text-muted"><i class="fa fa-clock me-1"></i><?= date('M j, Y g:i A', strtotime($en['created_at'])) ?></span>
          <span class="small text-muted">IP: <?= e($en['ip']) ?></span>
        </div>
        <table class="table table-sm mb-0">
          <?php foreach ($d as $label => $value): ?>
            <tr><th style="width:30%"><?= e($label) ?></th><td><?= nl2br(e($value)) ?></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div></div>

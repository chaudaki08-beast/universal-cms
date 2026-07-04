<?php
$cards = [
  ['Pages', $stats['pages'], 'fa-file-lines', '#2563eb', 'admin/pages'],
  ['Blog Posts', $stats['posts'], 'fa-newspaper', '#16a34a', 'admin/posts'],
  ['Products', $stats['products'], 'fa-box', '#f59e0b', 'admin/products'],
  ['Media Files', $stats['media'], 'fa-photo-film', '#8b5cf6', 'admin/media'],
  ['Orders', $stats['orders'], 'fa-bag-shopping', '#ec4899', 'admin/orders'],
  ['Unread Inquiries', $stats['entries'], 'fa-envelope', '#ef4444', 'admin/forms'],
];
?>
<div class="stat-grid">
  <?php foreach ($cards as [$label,$num,$icon,$color,$link]): ?>
    <a href="<?= base_url($link) ?>" class="stat-card text-decoration-none text-dark">
      <div class="stat-ico" style="background:<?= $color ?>"><i class="fa <?= $icon ?>"></i></div>
      <div><div class="num"><?= (int)$num ?></div><div class="lbl"><?= $label ?></div></div>
    </a>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header bg-white fw-bold d-flex justify-content-between">
        <span>Recently Updated Pages</span><a href="<?= base_url('admin/pages') ?>" class="small">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>Title</th><th>Status</th><th>Updated</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($recentPages as $p): ?>
              <tr>
                <td><?= e($p['title']) ?></td>
                <td><span class="badge-status badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                <td class="small text-muted"><?= date('M j, Y', strtotime($p['updated_at'])) ?></td>
                <td class="text-end"><a href="<?= base_url('admin/pages/edit/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($recentPages)): ?><tr><td colspan="4" class="text-muted text-center py-3">No pages yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-header bg-white fw-bold">Revenue & Inquiries</div>
      <div class="card-body">
        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
          <span class="text-muted">Total Revenue</span>
          <strong class="fs-5"><?= money($stats['revenue']) ?></strong>
        </div>
        <h6 class="fw-bold mb-3">Latest Form Submissions</h6>
        <?php foreach ($recentEntries as $en): $d = json_field($en['data']); ?>
          <div class="d-flex justify-content-between small py-2 border-bottom">
            <span><i class="fa fa-circle text-primary me-2" style="font-size:7px;vertical-align:middle"></i><?= e($en['form_name'] ?? 'Form') ?></span>
            <span class="text-muted"><?= date('M j', strtotime($en['created_at'])) ?></span>
          </div>
        <?php endforeach; ?>
        <?php if (empty($recentEntries)): ?><p class="text-muted small">No submissions yet.</p><?php endif; ?>
      </div>
    </div>
  </div>
</div>

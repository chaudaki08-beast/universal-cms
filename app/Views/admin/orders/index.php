<div class="page-head"><h2>Orders</h2></div>
<div class="card"><div class="table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td class="fw-semibold"><?= e($o['order_number']) ?></td>
          <td><?= e($o['customer_name']) ?><div class="small text-muted"><?= e($o['customer_email']) ?></div></td>
          <td><?= money($o['total']) ?></td>
          <td><span class="badge bg-secondary text-capitalize"><?= e($o['status']) ?></span></td>
          <td class="small text-muted"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
          <td class="text-end"><a href="<?= base_url('admin/orders/view/'.$o['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?><tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>

<?php $items = json_field($order['items']); ?>
<div class="page-head"><h2>Order <?= e($order['order_number']) ?></h2><a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary">Back</a></div>
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card"><div class="card-header bg-white fw-bold">Items</div><div class="table-responsive">
      <table class="table mb-0"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr><td><?= e($it['name']) ?></td><td><?= (int)$it['qty'] ?></td><td><?= money($it['price']) ?></td><td class="text-end"><?= money($it['price']*$it['qty']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end"><?= money($order['total']) ?></th></tr></tfoot>
      </table>
    </div></div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-3"><div class="card-header bg-white fw-bold">Customer</div><div class="card-body small">
      <p class="mb-1"><strong><?= e($order['customer_name']) ?></strong></p>
      <p class="mb-1"><?= e($order['customer_email']) ?></p>
      <p class="mb-1"><?= e($order['customer_phone']) ?></p>
      <p class="mb-0 text-muted">Payment: <?= e($order['payment_method']) ?></p>
      <?php if ($order['notes']): ?><hr><p class="mb-0"><?= e($order['notes']) ?></p><?php endif; ?>
    </div></div>
    <div class="card"><div class="card-header bg-white fw-bold">Status</div><div class="card-body">
      <form method="post" action="<?= base_url('admin/orders/updateStatus/'.$order['id']) ?>">
        <?= csrf_field() ?>
        <select name="status" class="form-select mb-3">
          <?php foreach (['pending','paid','processing','shipped','completed','cancelled'] as $st): ?>
            <option value="<?= $st ?>" <?= $order['status']===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-primary w-100">Update Status</button>
      </form>
    </div></div>
  </div>
</div>

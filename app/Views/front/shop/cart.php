<div class="container section-pad">
  <h1 class="mb-4">Your Cart</h1>
  <?php if (empty($items)): ?>
    <p class="text-muted">Your cart is empty. <a href="<?= base_url('shop') ?>">Continue shopping →</a></p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['product']['name']) ?></td>
              <td><?= money($it['price']) ?></td>
              <td><?= (int)$it['qty'] ?></td>
              <td class="text-end"><?= money($it['line']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Subtotal</th><th class="text-end"><?= money($subtotal) ?></th></tr></tfoot>
      </table>
    </div>
    <div class="text-end">
      <a href="<?= base_url('shop') ?>" class="btn btn-outline-secondary">Continue Shopping</a>
      <a href="<?= base_url('checkout') ?>" class="btn btn-theme">Proceed to Checkout →</a>
    </div>
  <?php endif; ?>
</div>

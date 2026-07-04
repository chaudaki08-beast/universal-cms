<div class="container section-pad" style="max-width:720px">
  <h1 class="mb-4">Checkout</h1>
  <form method="post" action="<?= base_url('checkout') ?>" class="cms-form">
    <?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Full Name *</label><input name="name" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Payment Method</label>
        <select name="payment_method" class="form-select">
          <option value="cod">Cash on Delivery</option>
          <option value="bank">Bank Transfer</option>
        </select>
      </div>
      <div class="col-12"><label class="form-label">Order Notes</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4">
      <strong class="fs-5">Total: <?= money($subtotal) ?></strong>
      <button class="btn btn-theme btn-lg">Place Order</button>
    </div>
  </form>
</div>

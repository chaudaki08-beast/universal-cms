<div class="container section-pad text-center" style="max-width:640px">
  <div class="feature-icon mx-auto" style="background:#dcfce7;color:#16a34a"><i class="fa fa-check fa-lg"></i></div>
  <h1 class="mt-3 mb-2">Thank you for your order!</h1>
  <p class="text-muted">Your order <strong><?= e($order['order_number']) ?></strong> has been received.</p>
  <p class="fs-5">Total: <strong><?= money($order['total']) ?></strong></p>
  <a href="<?= base_url('shop') ?>" class="btn btn-theme mt-3">Continue Shopping</a>
</div>

<div class="container section-pad text-center">
  <h1 class="display-5 mb-3">Welcome to <?= e($site['name'] ?? 'your new website') ?> 🎉</h1>
  <p class="lead text-muted mb-4">Your CMS is installed. Log in to the admin panel to build your home page.</p>
  <a href="<?= admin_url() ?>" class="btn btn-theme btn-lg">Go to Admin Panel</a>
</div>

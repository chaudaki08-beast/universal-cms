<?php $flashes = $flashes ?? []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <style>
    body{font-family:system-ui,Segoe UI,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;
      background:linear-gradient(135deg,#1e293b,#0f172a);padding:20px}
    .login-card{background:#fff;border-radius:18px;box-shadow:0 30px 70px rgba(0,0,0,.4);width:100%;max-width:410px;overflow:hidden}
    .login-head{background:#0f172a;color:#fff;text-align:center;padding:34px 24px}
    .login-head i{font-size:34px;color:#2563eb}
    .login-head h1{font-size:20px;margin:12px 0 2px}
    .login-head p{opacity:.6;font-size:13px;margin:0}
    .login-body{padding:32px 30px}
    .form-control{padding:12px 14px;border-radius:10px}
    .btn-login{background:#2563eb;border:0;padding:12px;border-radius:10px;font-weight:600;width:100%}
    .btn-login:hover{background:#1d4ed8}
    label{font-weight:600;font-size:13px;margin-bottom:5px}
    .input-icon{position:relative}.input-icon i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8}
    .input-icon .form-control{padding-left:40px}
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-head">
      <i class="fa fa-cube"></i>
      <h1>Universal CMS</h1>
      <p>Admin Panel Login</p>
    </div>
    <div class="login-body">
      <?php foreach ($flashes as $type => $messages):
        $cls = $type === 'error' ? 'danger' : ($type === 'success' ? 'success' : 'info');
        foreach ($messages as $m): ?>
          <div class="alert alert-<?= $cls ?> py-2 small"><?= e($m) ?></div>
      <?php endforeach; endforeach; ?>

      <form method="post" action="<?= base_url('admin/login') ?>">
        <?= csrf_field() ?>
        <label>Email Address</label>
        <div class="input-icon mb-3">
          <i class="fa fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
        </div>
        <label>Password</label>
        <div class="input-icon mb-4">
          <i class="fa fa-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button class="btn btn-login text-white">Sign In <i class="fa fa-arrow-right ms-1"></i></button>
      </form>
    </div>
  </div>
</body>
</html>

<?php
$flashes = $flashes ?? [];
if (!$flashes) return;
?>
<div class="cms-toast-wrap">
  <?php foreach ($flashes as $type => $messages):
    $map = ['success'=>'success','error'=>'danger','info'=>'info','warning'=>'warning'];
    $cls = $map[$type] ?? 'secondary';
    foreach ($messages as $msg): ?>
      <div class="cms-toast alert alert-<?= $cls ?> shadow"><?= e($msg) ?></div>
  <?php endforeach; endforeach; ?>
</div>

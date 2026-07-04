<?php $d = $data; ?>
<div class="field-row">
  <label>Custom HTML</label>
  <textarea name="data[html]" class="form-control" rows="8" style="font-family:monospace;font-size:13px"><?= e($d['html'] ?? '') ?></textarea>
  <small class="text-muted">Raw HTML. &lt;script&gt; tags are stripped for security.</small>
</div>

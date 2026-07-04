<?php $d = $data; ?>
<div class="field-row"><label>Heading</label><input name="data[heading]" class="form-control" value="<?= e($d['heading'] ?? '') ?>"></div>
<div class="field-row">
  <label>Content (HTML allowed)</label>
  <textarea name="data[body]" class="form-control" rows="6"><?= e($d['body'] ?? '') ?></textarea>
  <small class="text-muted">You can use HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;&lt;li&gt;, &lt;a&gt;. Scripts are removed for security.</small>
</div>

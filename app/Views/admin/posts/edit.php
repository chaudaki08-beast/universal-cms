<?php $p = $post; ?>
<div class="page-head"><h2><?= $p ? 'Edit Post' : 'New Post' ?></h2><a href="<?= base_url('admin/posts') ?>" class="btn btn-outline-secondary">Back</a></div>

<form method="post" action="<?= base_url('admin/posts/store') ?>">
  <?= csrf_field() ?>
  <?php if ($p): ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card"><div class="card-body">
        <div class="field-row"><label>Title</label><input name="title" id="postTitle" data-slug-source="#postSlug" class="form-control" value="<?= e($p['title'] ?? '') ?>" required></div>
        <div class="field-row"><label>Slug</label><input name="slug" id="postSlug" class="form-control" value="<?= e($p['slug'] ?? '') ?>"></div>
        <div class="field-row"><label>Excerpt</label><textarea name="excerpt" class="form-control" rows="2"><?= e($p['excerpt'] ?? '') ?></textarea></div>
        <div class="field-row"><label>Content (HTML allowed)</label><textarea name="body" class="form-control" rows="12"><?= e($p['body'] ?? '') ?></textarea></div>
      </div></div>
      <div class="card mt-3"><div class="card-header bg-white fw-bold">SEO</div><div class="card-body">
        <div class="field-row"><label>Meta Title</label><input name="meta_title" class="form-control" value="<?= e($p['meta_title'] ?? '') ?>"></div>
        <div class="field-row"><label>Meta Description</label><textarea name="meta_description" class="form-control" rows="2"><?= e($p['meta_description'] ?? '') ?></textarea></div>
      </div></div>
    </div>
    <div class="col-lg-4">
      <div class="card"><div class="card-body">
        <div class="field-row"><label>Status</label>
          <select name="status" class="form-select">
            <option value="draft" <?= ($p['status']??'')==='draft'?'selected':'' ?>>Draft</option>
            <option value="published" <?= ($p['status']??'')==='published'?'selected':'' ?>>Published</option>
          </select>
        </div>
        <div class="field-row"><label>Category</label>
          <select name="category_id" class="form-select">
            <option value="">— none —</option>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= ($p['category_id']??null)==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="field-row"><label>Tags (comma separated)</label><input name="tags" class="form-control" value="<?= e($p['tags'] ?? '') ?>"></div>
        <div class="field-row"><label>Featured Image</label>
          <div class="media-input-group">
            <img id="featured_image_preview" class="media-preview" src="<?= !empty($p['featured_image']) ? e(uploads_url($p['featured_image'])) : '' ?>" style="<?= empty($p['featured_image'])?'display:none':'' ?>">
            <input type="text" id="featured_image" name="featured_image" class="form-control" value="<?= e($p['featured_image'] ?? '') ?>" readonly>
            <button type="button" class="btn btn-outline-secondary" onclick="openMediaPicker('featured_image','single')"><i class="fa fa-image"></i></button>
          </div>
        </div>
        <button class="btn btn-primary w-100"><i class="fa fa-save me-1"></i>Save Post</button>
      </div></div>
    </div>
  </div>
</form>

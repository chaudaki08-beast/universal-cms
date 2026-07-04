<article class="container section-pad" style="max-width:820px">
  <a href="<?= base_url('blog') ?>" class="text-muted small"><i class="fa fa-arrow-left me-1"></i>Back to Blog</a>
  <h1 class="mt-3 mb-3"><?= e($post['title']) ?></h1>
  <p class="text-muted mb-4">
    <?php if (!empty($post['author_name'])): ?><i class="fa fa-user me-1"></i><?= e($post['author_name']) ?> · <?php endif; ?>
    <i class="fa fa-calendar me-1"></i><?= date('F j, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
    <?php if (!empty($post['category_name'])): ?> · <span class="badge bg-light text-dark"><?= e($post['category_name']) ?></span><?php endif; ?>
  </p>
  <?php if (!empty($post['featured_image'])): ?>
    <img src="<?= e(uploads_url($post['featured_image'])) ?>" class="img-fluid rounded mb-4" alt="">
  <?php endif; ?>
  <div class="rich-text"><?= $post['body'] /* sanitized on save */ ?></div>

  <?php if (!empty($post['tags'])): ?>
    <div class="mt-4">
      <?php foreach (explode(',', $post['tags']) as $tag): if(trim($tag)==='')continue; ?>
        <span class="badge bg-light text-dark me-1">#<?= e(trim($tag)) ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</article>

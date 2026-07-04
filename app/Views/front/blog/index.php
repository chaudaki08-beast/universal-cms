<div class="container section-pad">
  <h1 class="section-heading text-center mb-5">Our Blog</h1>
  <div class="row g-4">
    <?php foreach ($posts as $post): ?>
      <div class="col-md-6 col-lg-4">
        <article class="feature-card h-100 text-start p-0 overflow-hidden">
          <?php if (!empty($post['featured_image'])): ?>
            <img src="<?= e(uploads_url($post['featured_image'])) ?>" class="feature-img" style="margin:0;border-radius:0" alt="">
          <?php endif; ?>
          <div class="p-4">
            <?php if (!empty($post['category_name'])): ?><span class="badge bg-light text-dark mb-2"><?= e($post['category_name']) ?></span><?php endif; ?>
            <h4 class="feature-title"><a href="<?= base_url('blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h4>
            <p class="feature-text"><?= e($post['excerpt'] ?? '') ?></p>
            <small class="text-muted"><i class="fa fa-calendar me-1"></i><?= date('M j, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?></small>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?><p class="text-center text-muted">No posts published yet.</p><?php endif; ?>
  </div>

  <?php if (($totalPages ?? 1) > 1): ?>
    <nav class="mt-5"><ul class="pagination justify-content-center">
      <?php for ($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?p=<?= $i ?>"><?= $i ?></a></li>
      <?php endfor; ?>
    </ul></nav>
  <?php endif; ?>
</div>

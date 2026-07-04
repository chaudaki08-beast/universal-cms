<?php
namespace App\Controllers\Front;

use App\Core\Request;
use App\Models\Post;

class BlogController extends FrontController
{
    public function index(): void
    {
        $page    = max(1, Request::int('p', 1));
        $perPage = 9;
        $offset  = ($page - 1) * $perPage;

        $posts = Post::published($perPage, $offset);
        $total = Post::publishedCount();

        $this->render('front/blog/index', [
            'posts'      => $posts,
            'page'       => $page,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
            'pageTitle'  => 'Blog',
        ]);
    }

    public function show(string $slug): void
    {
        $post = Post::bySlug($slug);
        if (!$post) {
            http_response_code(404);
            $this->render('front/404', ['pageTitle' => 'Not Found']);
            return;
        }
        $this->render('front/blog/show', [
            'post'      => $post,
            'pageTitle' => $post['meta_title'] ?: $post['title'],
            'metaDescription' => $post['meta_description'] ?? $post['excerpt'] ?? '',
            'ogImage'   => $post['featured_image'] ?? '',
        ]);
    }
}

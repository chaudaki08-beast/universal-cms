<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Models\Post;
use App\Models\Category;

class PostsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('posts.manage');
    }

    public function index(): void
    {
        $posts = Database::all(
            "SELECT p.*, c.name AS category_name FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC"
        );
        $this->adminView('admin/posts/index', compact('posts'), 'Blog Posts');
    }

    public function create(): void
    {
        $post = null;
        $categories = Category::ofType('post');
        $this->adminView('admin/posts/edit', compact('post', 'categories'), 'New Post');
    }

    public function edit(string $id): void
    {
        $post = Post::find((int) $id);
        if (!$post) { redirect('admin/posts'); }
        $categories = Category::ofType('post');
        $this->adminView('admin/posts/edit', compact('post', 'categories'), 'Edit Post');
    }

    public function store(): void
    {
        $id = Request::int('id') ?: null;
        $title = Request::str('title');
        if ($title === '') { Session::flash('error', 'Title required.'); $this->back(); }

        $data = [
            'title'        => $title,
            'slug'         => Request::str('slug') ?: slugify($title),
            'excerpt'      => Request::str('excerpt'),
            'body'         => $this->stripScripts(Request::str('body')),
            'featured_image' => Request::str('featured_image'),
            'category_id'  => Request::int('category_id') ?: null,
            'tags'         => Request::str('tags'),
            'status'       => Request::str('status', 'draft'),
            'meta_title'   => Request::str('meta_title'),
            'meta_description' => Request::str('meta_description'),
            'author_id'    => Auth::id(),
        ];
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($id) {
            Post::updateById($id, $data);
        } else {
            $id = Post::create($data);
        }
        Session::flash('success', 'Post saved.');
        redirect('admin/posts/edit/' . $id);
    }

    public function destroy(string $id): void
    {
        Post::deleteById((int) $id);
        Session::flash('success', 'Post deleted.');
        redirect('admin/posts');
    }

    private function stripScripts(string $html): string
    {
        return preg_replace('#<script\b[^>]*>(.*?)</script>#is', '', $html);
    }
}

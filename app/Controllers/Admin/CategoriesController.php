<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\Category;

class CategoriesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('posts.manage');
    }

    public function index(): void
    {
        $type = Request::str('type', 'post');
        if (!in_array($type, ['post', 'product'], true)) { $type = 'post'; }
        $categories = Category::ofType($type);
        $this->adminView('admin/categories/index', compact('categories', 'type'), 'Categories');
    }

    public function store(): void
    {
        $name = Request::str('name');
        $type = Request::str('type', 'post');
        if ($name === '') { Session::flash('error', 'Name required.'); $this->back(); }

        Category::create([
            'type'        => in_array($type, ['post','product'], true) ? $type : 'post',
            'name'        => $name,
            'slug'        => Request::str('slug') ?: slugify($name),
            'description' => Request::str('description'),
        ]);
        Session::flash('success', 'Category added.');
        redirect('admin/categories/index?type=' . $type);
    }

    public function destroy(string $id): void
    {
        $cat = Category::find((int) $id);
        Category::deleteById((int) $id);
        Session::flash('success', 'Category deleted.');
        redirect('admin/categories/index?type=' . ($cat['type'] ?? 'post'));
    }
}

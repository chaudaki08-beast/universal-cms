<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\Product;
use App\Models\Category;

class ProductsController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('products.manage');
    }

    public function index(): void
    {
        $products = Product::all('created_at DESC');
        $this->adminView('admin/products/index', compact('products'), 'Products');
    }

    public function create(): void
    {
        $product = null;
        $categories = Category::ofType('product');
        $this->adminView('admin/products/edit', compact('product', 'categories'), 'New Product');
    }

    public function edit(string $id): void
    {
        $product = Product::find((int) $id);
        if (!$product) { redirect('admin/products'); }
        $categories = Category::ofType('product');
        $this->adminView('admin/products/edit', compact('product', 'categories'), 'Edit Product');
    }

    public function store(): void
    {
        $id = Request::int('id') ?: null;
        $name = Request::str('name');
        if ($name === '') { Session::flash('error', 'Name required.'); $this->back(); }

        $images = array_values(array_filter(Request::array('images')));

        $data = [
            'name'        => $name,
            'slug'        => Request::str('slug') ?: slugify($name),
            'sku'         => Request::str('sku'),
            'description' => Request::str('description'),
            'short_description' => Request::str('short_description'),
            'price'       => Request::float('price'),
            'sale_price'  => Request::str('sale_price') !== '' ? Request::float('sale_price') : null,
            'stock'       => Request::int('stock'),
            'category_id' => Request::int('category_id') ?: null,
            'images'      => json_encode($images),
            'status'      => Request::str('status', 'draft'),
            'featured'    => Request::bool('featured') ? 1 : 0,
            'meta_title'  => Request::str('meta_title'),
            'meta_description' => Request::str('meta_description'),
        ];

        if ($id) { Product::updateById($id, $data); }
        else { $id = Product::create($data); }

        Session::flash('success', 'Product saved.');
        redirect('admin/products/edit/' . $id);
    }

    public function destroy(string $id): void
    {
        Product::deleteById((int) $id);
        Session::flash('success', 'Product deleted.');
        redirect('admin/products');
    }
}

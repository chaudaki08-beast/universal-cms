<?php
namespace App\Controllers\Front;

use App\Core\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends FrontController
{
    public function index(): void
    {
        $page    = max(1, Request::int('p', 1));
        $perPage = 12;
        $offset  = ($page - 1) * $perPage;

        $products   = Product::published($perPage, $offset);
        $total      = Product::count("status='published'");
        $categories = Category::ofType('product');

        $this->render('front/shop/index', [
            'products'   => $products,
            'categories' => $categories,
            'page'       => $page,
            'totalPages' => max(1, (int) ceil($total / $perPage)),
            'pageTitle'  => 'Shop',
        ]);
    }

    public function category(string $slug): void
    {
        $category = Category::bySlug($slug, 'product');
        if (!$category) { http_response_code(404); $this->render('front/404', ['pageTitle' => 'Not Found']); return; }

        $products   = Product::published(48, 0, (int) $category['id']);
        $categories = Category::ofType('product');
        $this->render('front/shop/index', [
            'products'   => $products,
            'categories' => $categories,
            'page'       => 1, 'totalPages' => 1,
            'activeCategory' => $category,
            'pageTitle'  => $category['name'],
        ]);
    }

    public function show(string $slug): void
    {
        $product = Product::bySlug($slug);
        if (!$product) { http_response_code(404); $this->render('front/404', ['pageTitle' => 'Not Found']); return; }

        $related = Product::published(4, 0, (int) ($product['category_id'] ?? 0));
        $this->render('front/shop/show', [
            'product'   => $product,
            'related'   => $related,
            'pageTitle' => $product['meta_title'] ?: $product['name'],
            'metaDescription' => $product['meta_description'] ?? $product['short_description'] ?? '',
        ]);
    }
}

<?php
namespace App\Controllers\Admin;

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Media;
use App\Models\Order;
use App\Models\FormEntry;
use App\Core\Database;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $stats = [
            'pages'    => Page::count(),
            'posts'    => Post::count(),
            'products' => Product::count(),
            'media'    => Media::count(),
            'orders'   => Order::count(),
            'entries'  => FormEntry::unreadCount(),
            'revenue'  => Order::revenueTotal(),
        ];

        $recentPages = Database::all(
            "SELECT id,title,slug,status,updated_at FROM pages ORDER BY updated_at DESC LIMIT 6"
        );
        $recentEntries = Database::all(
            "SELECT fe.*, f.name AS form_name FROM form_entries fe
             LEFT JOIN forms f ON f.id = fe.form_id
             ORDER BY fe.created_at DESC LIMIT 6"
        );

        $this->adminView('admin/dashboard', compact('stats', 'recentPages', 'recentEntries'), 'Dashboard');
    }
}

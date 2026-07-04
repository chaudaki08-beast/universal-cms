<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\Order;

class OrdersController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('products.manage');
    }

    public function index(): void
    {
        $orders = Order::all('created_at DESC');
        $this->adminView('admin/orders/index', compact('orders'), 'Orders');
    }

    public function view(string $id): void
    {
        $order = Order::find((int) $id);
        if (!$order) { redirect('admin/orders'); }
        $this->adminView('admin/orders/view', compact('order'), 'Order ' . $order['order_number']);
    }

    public function updateStatus(string $id): void
    {
        Order::updateById((int) $id, ['status' => Request::str('status', 'pending')]);
        Session::flash('success', 'Order status updated.');
        redirect('admin/orders/view/' . $id);
    }
}

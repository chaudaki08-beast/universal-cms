<?php
namespace App\Controllers\Front;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;
use App\Models\Product;
use App\Models\Order;

/**
 * Session-based cart + simple checkout that records an order.
 * Payment gateway integration point is marked below.
 */
class CartController extends FrontController
{
    public function index(): void
    {
        $cart = $this->cart();
        $items = []; $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $p = Product::find((int) $pid);
            if (!$p) continue;
            $price = Product::effectivePrice($p);
            $line  = $price * $qty;
            $subtotal += $line;
            $items[] = ['product' => $p, 'qty' => $qty, 'price' => $price, 'line' => $line];
        }
        $this->render('front/shop/cart', [
            'items' => $items, 'subtotal' => $subtotal, 'pageTitle' => 'Your Cart',
        ]);
    }

    public function add(): void
    {
        Csrf::verify();
        $pid = Request::int('product_id');
        $qty = max(1, Request::int('qty', 1));
        if ($pid && Product::find($pid)) {
            $cart = $this->cart();
            $cart[$pid] = ($cart[$pid] ?? 0) + $qty;
            Session::set('_cart', $cart);
            Session::flash('success', 'Added to cart.');
        }
        redirect('cart');
    }

    public function checkout(): void
    {
        $cart = $this->cart();
        if (!$cart) { redirect('shop'); }

        if (Request::isPost()) {
            Csrf::verify();
            $items = []; $subtotal = 0;
            foreach ($cart as $pid => $qty) {
                $p = Product::find((int) $pid);
                if (!$p) continue;
                $price = Product::effectivePrice($p);
                $subtotal += $price * $qty;
                $items[] = ['id' => $p['id'], 'name' => $p['name'], 'qty' => $qty, 'price' => $price];
            }

            $orderId = Order::create([
                'order_number'   => Order::generateNumber(),
                'customer_name'  => Request::str('name'),
                'customer_email' => Request::str('email'),
                'customer_phone' => Request::str('phone'),
                'items'          => json_encode($items),
                'subtotal'       => $subtotal,
                'tax'            => 0,
                'shipping'       => 0,
                'total'          => $subtotal,
                'status'         => 'pending',
                'payment_method' => Request::str('payment_method', 'cod'),
                'notes'          => Request::str('notes'),
            ]);

            // --- Payment gateway hook -------------------------------------
            // Integrate Stripe/PayPal/Razorpay here using the $orderId/total.
            // On success, set status='paid'. Structure is ready for it.
            // --------------------------------------------------------------

            Session::forget('_cart');
            $order = Order::find($orderId);
            $this->render('front/shop/order-confirmed', [
                'order' => $order, 'pageTitle' => 'Order Confirmed',
            ]);
            return;
        }

        // Recompute totals for display
        $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $p = Product::find((int) $pid);
            if ($p) { $subtotal += Product::effectivePrice($p) * $qty; }
        }
        $this->render('front/shop/checkout', ['subtotal' => $subtotal, 'pageTitle' => 'Checkout']);
    }

    private function cart(): array
    {
        $c = Session::get('_cart', []);
        return is_array($c) ? $c : [];
    }
}

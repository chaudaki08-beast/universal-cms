<?php
namespace App\Core;

/**
 * Application kernel: boots services and dispatches the request.
 *
 * Routing strategy (cPanel friendly, no external router lib):
 *   /                       -> Front\HomeController@index
 *   /admin                  -> Admin\DashboardController@index
 *   /admin/{controller}/{action}/{id?}
 *   /{slug}                 -> Front\PageController@show  (dynamic CMS page)
 *   /blog, /blog/{slug}     -> Front\BlogController
 *   /shop, /product/{slug}  -> Front\ShopController
 */
class App
{
    protected Router $router;

    public function __construct()
    {
        Session::start();
        $this->router = new Router();
        $this->registerRoutes();
    }

    public function run(): void
    {
        $uri = $this->currentUri();
        $this->router->dispatch($uri, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /** Path relative to the app base, no query string. */
    protected function currentUri(): string
    {
        $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    protected function registerRoutes(): void
    {
        $r = $this->router;

        // ---- Auth ----
        $r->any('/admin/login',  'Admin\\AuthController@login');
        $r->any('/admin/logout', 'Admin\\AuthController@logout');

        // ---- Admin (prefix dispatch handled inside controllers) ----
        $r->any('/admin',                 'Admin\\DashboardController@index');
        $r->prefix('/admin', 'Admin');    // /admin/{controller}/{action}/{id}

        // ---- Front: special modules ----
        $r->get('/',                'Front\\PageController@home');
        $r->get('/blog',            'Front\\BlogController@index');
        $r->get('/blog/{slug}',     'Front\\BlogController@show');
        $r->get('/shop',            'Front\\ShopController@index');
        $r->get('/product/{slug}',  'Front\\ShopController@show');
        $r->get('/category/{slug}', 'Front\\ShopController@category');
        $r->any('/cart',            'Front\\CartController@index');
        $r->any('/checkout',        'Front\\CartController@checkout');
        $r->post('/cart/add',       'Front\\CartController@add');

        // ---- SEO endpoints ----
        $r->get('/sitemap.xml',     'Front\\SeoController@sitemap');
        $r->get('/robots.txt',      'Front\\SeoController@robots');

        // ---- Form submissions ----
        $r->post('/form/submit',    'Front\\FormController@submit');

        // ---- Catch-all dynamic CMS page (MUST be last) ----
        $r->get('/{slug}',          'Front\\PageController@show');
    }
}

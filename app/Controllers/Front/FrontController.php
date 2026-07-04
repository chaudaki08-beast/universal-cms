<?php
namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\View;
use App\Core\Session;
use App\Models\Menu;
use App\Models\Setting;

/** Base front controller: assembles shared layout data (menus, settings, theme). */
abstract class FrontController extends Controller
{
    protected function render(string $view, array $data = []): void
    {
        $shared = [
            'site'        => [
                'name'    => Setting::get('site_name', 'My Website'),
                'tagline' => Setting::get('site_tagline', ''),
                'logo'    => Setting::get('logo', ''),
                'favicon' => Setting::get('favicon', ''),
            ],
            'theme'       => Setting::group('theme'),
            'social'      => Setting::group('social'),
            'contact'     => [
                'email'   => Setting::get('contact_email', ''),
                'phone'   => Setting::get('contact_phone', ''),
                'address' => Setting::get('contact_address', ''),
            ],
            'primaryMenu' => Menu::tree('primary'),
            'footerMenu'  => Menu::tree('footer'),
            'flashes'     => Session::getFlashes(),
            'showHeader'  => $data['showHeader'] ?? true,
            'showFooter'  => $data['showFooter'] ?? true,
        ];
        View::render($view, array_merge($shared, $data), 'front');
    }
}

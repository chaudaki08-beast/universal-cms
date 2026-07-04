<?php
namespace App\Controllers\Front;

use App\Models\Page;
use App\Models\Setting;

class PageController extends FrontController
{
    public function home(): void
    {
        $page = Page::home();
        if (!$page) {
            // No home page yet — friendly placeholder.
            $this->render('front/welcome', ['pageTitle' => Setting::get('site_name', 'Welcome')]);
            return;
        }
        $this->renderPage($page);
    }

    public function show(string $slug): void
    {
        // Reserved single-segment slugs handled by their own routes already.
        $page = Page::bySlug($slug);
        if (!$page) {
            http_response_code(404);
            $this->render('front/404', ['pageTitle' => 'Not Found']);
            return;
        }
        $this->renderPage($page);
    }

    protected function renderPage(array $page): void
    {
        $sections = Page::sections((int) $page['id'], true);

        $this->render('front/page', [
            'page'       => $page,
            'sections'   => $sections,
            'pageTitle'  => $page['meta_title'] ?: $page['title'],
            'metaDescription' => $page['meta_description'] ?? '',
            'metaKeywords'    => $page['meta_keywords'] ?? '',
            'ogImage'    => $page['og_image'] ?? '',
            'canonical'  => $page['canonical_url'] ?? '',
            'showHeader' => (int) $page['show_header'] === 1,
            'showFooter' => (int) $page['show_footer'] === 1,
        ]);
    }
}

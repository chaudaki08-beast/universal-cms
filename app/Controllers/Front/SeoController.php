<?php
namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Setting;

class SeoController extends Controller
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $urls = [];
        $urls[] = ['loc' => base_url('/'), 'priority' => '1.0'];

        foreach (Database::all("SELECT slug, updated_at FROM pages WHERE status='published'") as $p) {
            if ($p['slug'] === 'home') continue;
            $urls[] = ['loc' => base_url($p['slug']), 'lastmod' => $p['updated_at'], 'priority' => '0.8'];
        }
        foreach (Database::all("SELECT slug, updated_at FROM posts WHERE status='published'") as $p) {
            $urls[] = ['loc' => base_url('blog/' . $p['slug']), 'lastmod' => $p['updated_at'], 'priority' => '0.6'];
        }
        foreach (Database::all("SELECT slug, updated_at FROM products WHERE status='published'") as $p) {
            $urls[] = ['loc' => base_url('product/' . $p['slug']), 'lastmod' => $p['updated_at'], 'priority' => '0.6'];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                echo "    <lastmod>" . date('Y-m-d', strtotime($u['lastmod'])) . "</lastmod>\n";
            }
            echo "    <priority>{$u['priority']}</priority>\n  </url>\n";
        }
        echo '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $custom = Setting::get('robots_txt', '');
        if ($custom) { echo $custom; return; }

        echo "User-agent: *\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /install\n";
        echo "Allow: /\n\n";
        echo "Sitemap: " . base_url('sitemap.xml') . "\n";
    }
}

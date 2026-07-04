<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Models\Page;
use App\Models\Template;
use App\Models\Form;

class PagesController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('pages.edit');
    }

    public function index(): void
    {
        $pages = Database::all(
            "SELECT p.*, t.name AS template_name FROM pages p
             LEFT JOIN templates t ON t.id = p.template_id
             ORDER BY p.is_home DESC, p.sort_order, p.title"
        );
        $this->adminView('admin/pages/index', compact('pages'), 'Pages');
    }

    public function create(): void
    {
        $templates = Template::active();
        $this->adminView('admin/pages/create', compact('templates'), 'New Page');
    }

    public function store(): void
    {
        $title = Request::str('title');
        if ($title === '') {
            Session::flash('error', 'Page title is required.');
            $this->back();
        }
        $slug = Request::str('slug') ?: slugify($title);
        $slug = $this->uniqueSlug($slug);

        $templateId = Request::int('template_id') ?: null;

        $pageId = Page::create([
            'title'       => $title,
            'slug'        => $slug,
            'template_id' => $templateId,
            'status'      => Request::str('status', 'draft'),
            'meta_title'  => $title,
            'author_id'   => Auth::id(),
        ]);

        // Seed sections from the chosen template blueprint
        if ($templateId) {
            $tpl = Template::find($templateId);
            $blueprint = json_field($tpl['blueprint'] ?? '[]');
            $order = 0;
            foreach ($blueprint as $s) {
                Database::insert('page_sections', [
                    'page_id'    => $pageId,
                    'type'       => $s['type'],
                    'title'      => $s['title'] ?? ucfirst($s['type']),
                    'data'       => json_encode($this->defaultData($s['type'])),
                    'settings'   => json_encode(['padding' => 'lg']),
                    'sort_order' => $order++,
                ]);
            }
        }

        Session::flash('success', 'Page created. Now build your sections.');
        redirect('admin/pages/edit/' . $pageId);
    }

    public function edit(string $id): void
    {
        $page = Page::find((int) $id);
        if (!$page) { Session::flash('error', 'Page not found.'); redirect('admin/pages'); }

        $sections  = Page::sections((int) $id, false);
        $templates = Template::active();
        $forms     = Form::all();
        $sectionTypes = $this->sectionTypes();

        $this->adminView('admin/pages/edit',
            compact('page', 'sections', 'templates', 'forms', 'sectionTypes'),
            'Edit: ' . $page['title']
        );
    }

    public function update(string $id): void
    {
        $page = Page::find((int) $id);
        if (!$page) { redirect('admin/pages'); }

        $slug = Request::str('slug') ?: slugify(Request::str('title'));
        if ($slug !== $page['slug']) { $slug = $this->uniqueSlug($slug, (int) $id); }

        Page::updateById((int) $id, [
            'title'            => Request::str('title', $page['title']),
            'slug'             => $slug,
            'status'           => Request::str('status', 'draft'),
            'show_header'      => Request::bool('show_header') ? 1 : 0,
            'show_footer'      => Request::bool('show_footer') ? 1 : 0,
            'meta_title'       => Request::str('meta_title'),
            'meta_description' => Request::str('meta_description'),
            'meta_keywords'    => Request::str('meta_keywords'),
            'og_image'         => Request::str('og_image'),
            'canonical_url'    => Request::str('canonical_url'),
        ]);

        if (Request::bool('is_home')) {
            Database::run("UPDATE pages SET is_home = 0");
            Page::updateById((int) $id, ['is_home' => 1, 'status' => 'published']);
        }

        Session::flash('success', 'Page settings saved.');
        redirect('admin/pages/edit/' . $id);
    }

    public function duplicate(string $id): void
    {
        $new = Page::duplicate((int) $id);
        Session::flash('success', $new ? 'Page duplicated.' : 'Could not duplicate page.');
        redirect($new ? 'admin/pages/edit/' . $new : 'admin/pages');
    }

    public function destroy(string $id): void
    {
        $page = Page::find((int) $id);
        if ($page && (int) $page['is_home'] === 1) {
            Session::flash('error', 'You cannot delete the home page. Set another page as home first.');
            redirect('admin/pages');
        }
        Page::deleteById((int) $id); // sections cascade
        Session::flash('success', 'Page deleted.');
        redirect('admin/pages');
    }

    /* ---------------- Section operations (AJAX + form) ---------------- */

    public function addSection(string $id): void
    {
        $pageId = (int) $id;
        $type   = Request::str('type', 'text');
        $maxOrder = (int) Database::scalar(
            "SELECT COALESCE(MAX(sort_order),-1) FROM page_sections WHERE page_id = ?", [$pageId]
        );
        Database::insert('page_sections', [
            'page_id'    => $pageId,
            'type'       => $type,
            'title'      => ucfirst($type),
            'data'       => json_encode($this->defaultData($type)),
            'settings'   => json_encode(['padding' => 'lg']),
            'sort_order' => $maxOrder + 1,
        ]);
        Session::flash('success', ucfirst($type) . ' section added.');
        redirect('admin/pages/edit/' . $pageId);
    }

    public function saveSection(string $id): void
    {
        $sectionId = (int) $id;
        $section = Database::first("SELECT * FROM page_sections WHERE id = ?", [$sectionId]);
        if (!$section) { $this->back(); }

        $data     = Request::array('data');      // section field values
        $settings = Request::array('settings');  // layout/style
        $data     = $this->sanitizeSectionData($section['type'], $data);

        Database::update('page_sections', [
            'title'      => Request::str('title', $section['title']),
            'data'       => json_encode($data),
            'settings'   => json_encode($settings),
            'is_visible' => Request::bool('is_visible') ? 1 : 0,
        ], ['id' => $sectionId]);

        Session::flash('success', 'Section saved.');
        redirect('admin/pages/edit/' . $section['page_id'] . '#section-' . $sectionId);
    }

    public function deleteSection(string $id): void
    {
        $section = Database::first("SELECT * FROM page_sections WHERE id = ?", [(int) $id]);
        Database::delete('page_sections', ['id' => (int) $id]);
        Session::flash('success', 'Section removed.');
        redirect('admin/pages/edit/' . ($section['page_id'] ?? ''));
    }

    /** AJAX: persist new order. Expects order[]=sectionId in sequence. */
    public function reorderSections(): void
    {
        $order = Request::array('order');
        foreach ($order as $i => $sectionId) {
            Database::run("UPDATE page_sections SET sort_order = ? WHERE id = ?", [(int) $i, (int) $sectionId]);
        }
        $this->json(['ok' => true]);
    }

    /* ---------------- helpers ---------------- */

    private function uniqueSlug(string $slug, int $ignoreId = 0): string
    {
        $base = $slug; $i = 1;
        while (true) {
            $row = Database::first(
                "SELECT id FROM pages WHERE slug = ? AND id <> ? LIMIT 1", [$slug, $ignoreId]
            );
            if (!$row) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }

    private function sectionTypes(): array
    {
        return [
            'flex' => '✦ Flexible Layout (Drag & Drop)',
            'hero' => 'Hero Banner', 'text' => 'Text Block', 'image' => 'Single Image',
            'gallery' => 'Image Gallery', 'cards' => 'Cards / Features', 'testimonials' => 'Testimonials',
            'faq' => 'FAQ Accordion', 'pricing' => 'Pricing Table', 'cta' => 'Call To Action',
            'contact' => 'Contact / Form', 'map' => 'Map', 'html' => 'Custom HTML',
        ];
    }

    /** XSS-aware sanitisation of incoming section data. */
    private function sanitizeSectionData(string $type, array $data): array
    {
        array_walk_recursive($data, function (&$v) {
            if (is_string($v)) { $v = trim($v); }
        });
        // Allow rich HTML only in known rich fields; strip <script> everywhere.
        $strip = function ($html) {
            return preg_replace('#<script\b[^>]*>(.*?)</script>#is', '', (string) $html);
        };
        foreach (['body', 'html', 'builder'] as $rich) {
            if (isset($data[$rich])) { $data[$rich] = $strip($data[$rich]); }
        }
        return $data;
    }

    private function defaultData(string $type): array
    {
        // Mirrors installer defaults so new sections aren't empty.
        return require_section_defaults($type);
    }
}

/** Shared default-data provider (kept here for the admin side). */
function require_section_defaults(string $type): array
{
    static $defaults = null;
    if ($defaults === null) {
        $defaults = [
            'hero' => ['heading' => 'Your Headline Here', 'subheading' => 'A short supporting subheading.',
                'button_text' => 'Get Started', 'button_link' => '#', 'button2_text' => '', 'button2_link' => '',
                'background_image' => '', 'overlay_color' => 'rgba(15,23,42,0.55)', 'align' => 'center', 'height' => 'large'],
            'text' => ['heading' => 'Section Heading', 'body' => '<p>Editable content goes here.</p>'],
            'image' => ['image' => '', 'caption' => '', 'align' => 'center'],
            'gallery' => ['heading' => 'Gallery', 'images' => []],
            'cards' => ['heading' => 'Highlights', 'columns' => 3, 'items' => [
                ['title' => 'Item One', 'text' => 'Description', 'icon' => 'fa-star', 'image' => '', 'link' => ''],
                ['title' => 'Item Two', 'text' => 'Description', 'icon' => 'fa-heart', 'image' => '', 'link' => ''],
                ['title' => 'Item Three', 'text' => 'Description', 'icon' => 'fa-bolt', 'image' => '', 'link' => ''],
            ]],
            'testimonials' => ['heading' => 'Testimonials', 'items' => [
                ['name' => 'Customer', 'role' => 'Client', 'quote' => 'Great experience!', 'avatar' => '', 'rating' => 5]]],
            'faq' => ['heading' => 'FAQ', 'items' => [['q' => 'Question?', 'a' => 'Answer.']]],
            'pricing' => ['heading' => 'Pricing', 'items' => [
                ['name' => 'Plan', 'price' => '19', 'period' => '/mo', 'features' => "Feature A\nFeature B",
                 'button_text' => 'Choose', 'button_link' => '#', 'featured' => false]]],
            'cta' => ['heading' => 'Ready to start?', 'subheading' => '', 'button_text' => 'Contact', 'button_link' => '/contact'],
            'flex' => ['heading' => '', 'builder' => '{"rows":[]}'],
            'contact' => ['heading' => 'Contact Us', 'form_slug' => 'contact', 'show_info' => true],
            'map' => ['heading' => 'Find Us', 'embed' => '', 'address' => ''],
            'html' => ['html' => '<!-- custom HTML -->'],
        ];
    }
    return $defaults[$type] ?? [];
}

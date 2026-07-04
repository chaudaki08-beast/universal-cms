<?php
namespace App\Models;

use App\Core\Database;

class Page extends Model
{
    protected static string $table = 'pages';

    public static function bySlug(string $slug, bool $publishedOnly = true): ?array
    {
        $sql = "SELECT * FROM pages WHERE slug = ?";
        if ($publishedOnly) { $sql .= " AND status = 'published'"; }
        return Database::first($sql . " LIMIT 1", [$slug]);
    }

    public static function home(): ?array
    {
        return Database::first("SELECT * FROM pages WHERE is_home = 1 AND status='published' LIMIT 1")
            ?? Database::first("SELECT * FROM pages WHERE slug='home' LIMIT 1");
    }

    public static function published(): array
    {
        return Database::all("SELECT * FROM pages WHERE status='published' ORDER BY sort_order, title");
    }

    public static function sections(int $pageId, bool $visibleOnly = true): array
    {
        $sql = "SELECT * FROM page_sections WHERE page_id = ?";
        if ($visibleOnly) { $sql .= " AND is_visible = 1"; }
        return Database::all($sql . " ORDER BY sort_order, id", [$pageId]);
    }

    /** Duplicate a page and all its sections. */
    public static function duplicate(int $id): ?int
    {
        $page = self::find($id);
        if (!$page) return null;

        unset($page['id']);
        $page['title']   .= ' (Copy)';
        $page['slug']     = $page['slug'] . '-copy-' . substr(md5((string)microtime(true)), 0, 4);
        $page['is_home']  = 0;
        $page['status']   = 'draft';
        unset($page['created_at'], $page['updated_at']);

        $newId = self::create($page);
        foreach (self::sections($id, false) as $s) {
            unset($s['id']);
            $s['page_id'] = $newId;
            Database::insert('page_sections', $s);
        }
        return $newId;
    }
}

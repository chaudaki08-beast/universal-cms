<?php
namespace App\Models;

use App\Core\Database;

class Post extends Model
{
    protected static string $table = 'posts';

    public static function bySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT p.*, c.name AS category_name, u.name AS author_name
             FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN users u ON u.id = p.author_id
             WHERE p.slug = ? AND p.status='published' LIMIT 1",
            [$slug]
        );
    }

    public static function published(int $limit = 12, int $offset = 0): array
    {
        return Database::all(
            "SELECT p.*, c.name AS category_name
             FROM posts p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status='published'
             ORDER BY COALESCE(p.published_at, p.created_at) DESC
             LIMIT $limit OFFSET $offset"
        );
    }

    public static function publishedCount(): int
    {
        return (int) Database::scalar("SELECT COUNT(*) FROM posts WHERE status='published'");
    }
}

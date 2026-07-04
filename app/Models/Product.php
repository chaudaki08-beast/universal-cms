<?php
namespace App\Models;

use App\Core\Database;

class Product extends Model
{
    protected static string $table = 'products';

    public static function bySlug(string $slug): ?array
    {
        return Database::first(
            "SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status='published' LIMIT 1",
            [$slug]
        );
    }

    public static function published(int $limit = 12, int $offset = 0, ?int $categoryId = null): array
    {
        $sql = "SELECT * FROM products WHERE status='published'";
        $params = [];
        if ($categoryId) { $sql .= " AND category_id = ?"; $params[] = $categoryId; }
        $sql .= " ORDER BY featured DESC, created_at DESC LIMIT $limit OFFSET $offset";
        return Database::all($sql, $params);
    }

    public static function featured(int $limit = 8): array
    {
        return Database::all(
            "SELECT * FROM products WHERE status='published' AND featured=1 ORDER BY created_at DESC LIMIT $limit"
        );
    }

    public static function effectivePrice(array $product): float
    {
        $sale = $product['sale_price'];
        return ($sale !== null && $sale !== '' && (float)$sale > 0)
            ? (float) $sale : (float) $product['price'];
    }
}

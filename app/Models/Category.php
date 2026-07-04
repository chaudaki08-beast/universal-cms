<?php
namespace App\Models;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function ofType(string $type): array
    {
        return self::where('type', $type, 'name');
    }

    public static function bySlug(string $slug, string $type = 'product'): ?array
    {
        return \App\Core\Database::first(
            "SELECT * FROM categories WHERE slug = ? AND type = ? LIMIT 1",
            [$slug, $type]
        );
    }
}

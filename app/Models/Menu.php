<?php
namespace App\Models;

use App\Core\Database;

class Menu extends Model
{
    protected static string $table = 'menus';

    public static function bySlug(string $slug): ?array
    {
        return self::firstWhere('slug', $slug);
    }

    /** Return menu items as a nested tree (one level of children). */
    public static function tree(string $slug): array
    {
        $menu = self::bySlug($slug);
        if (!$menu) return [];

        $items = Database::all(
            "SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order, id",
            [$menu['id']]
        );

        $byParent = [];
        foreach ($items as $i) {
            $byParent[$i['parent_id'] ?? 0][] = $i;
        }
        $build = function ($parentId) use (&$build, $byParent) {
            $branch = [];
            foreach ($byParent[$parentId] ?? [] as $item) {
                $item['children'] = $build($item['id']);
                $branch[] = $item;
            }
            return $branch;
        };
        return $build(0);
    }

    public static function items(int $menuId): array
    {
        return Database::all(
            "SELECT * FROM menu_items WHERE menu_id = ? ORDER BY sort_order, id",
            [$menuId]
        );
    }
}

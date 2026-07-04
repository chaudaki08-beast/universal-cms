<?php
namespace App\Models;

use App\Core\Database;

class Media extends Model
{
    protected static string $table = 'media';

    public static function search(string $q = '', string $folder = ''): array
    {
        $sql = "SELECT * FROM media WHERE 1";
        $params = [];
        if ($q !== '') { $sql .= " AND name LIKE ?"; $params[] = "%$q%"; }
        if ($folder !== '') { $sql .= " AND folder = ?"; $params[] = $folder; }
        return Database::all($sql . " ORDER BY created_at DESC LIMIT 300", $params);
    }

    public static function folders(): array
    {
        $rows = Database::all("SELECT DISTINCT folder FROM media ORDER BY folder");
        return array_column($rows, 'folder');
    }
}

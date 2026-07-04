<?php
namespace App\Models;

use App\Core\Database;

/** Lightweight active-record-ish base. Child sets $table. */
abstract class Model
{
    protected static string $table = '';

    public static function find(int $id): ?array
    {
        return Database::first("SELECT * FROM `" . static::$table . "` WHERE id = ?", [$id]);
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::all("SELECT * FROM `" . static::$table . "` ORDER BY $orderBy");
    }

    public static function where(string $column, $value, string $orderBy = 'id DESC'): array
    {
        return Database::all(
            "SELECT * FROM `" . static::$table . "` WHERE `$column` = ? ORDER BY $orderBy",
            [$value]
        );
    }

    public static function firstWhere(string $column, $value): ?array
    {
        return Database::first(
            "SELECT * FROM `" . static::$table . "` WHERE `$column` = ? LIMIT 1",
            [$value]
        );
    }

    public static function create(array $data): int
    {
        return (int) Database::insert(static::$table, $data);
    }

    public static function updateById(int $id, array $data): int
    {
        return Database::update(static::$table, $data, ['id' => $id]);
    }

    public static function deleteById(int $id): int
    {
        return Database::delete(static::$table, ['id' => $id]);
    }

    public static function count(string $where = '1', array $params = []): int
    {
        return (int) Database::scalar(
            "SELECT COUNT(*) FROM `" . static::$table . "` WHERE $where",
            $params
        );
    }
}

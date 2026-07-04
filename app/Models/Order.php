<?php
namespace App\Models;

use App\Core\Database;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function generateNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public static function recent(int $limit = 10): array
    {
        return Database::all("SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit");
    }

    public static function revenueTotal(): float
    {
        return (float) Database::scalar(
            "SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('paid','processing','shipped','completed')"
        );
    }
}

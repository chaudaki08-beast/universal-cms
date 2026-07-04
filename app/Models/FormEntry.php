<?php
namespace App\Models;

use App\Core\Database;

class FormEntry extends Model
{
    protected static string $table = 'form_entries';

    public static function forForm(int $formId): array
    {
        return Database::all(
            "SELECT * FROM form_entries WHERE form_id = ? ORDER BY created_at DESC",
            [$formId]
        );
    }

    public static function unreadCount(): int
    {
        return (int) Database::scalar("SELECT COUNT(*) FROM form_entries WHERE is_read = 0");
    }
}

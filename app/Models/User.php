<?php
namespace App\Models;

use App\Core\Database;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return Database::first("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public static function touchLogin(int $id): void
    {
        Database::run("UPDATE users SET last_login = NOW() WHERE id = ?", [$id]);
    }

    public static function updatePassword(int $id, string $plain): void
    {
        Database::run(
            "UPDATE users SET password = ? WHERE id = ?",
            [password_hash($plain, PASSWORD_DEFAULT), $id]
        );
    }
}

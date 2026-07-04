<?php
namespace App\Models;

class Form extends Model
{
    protected static string $table = 'forms';

    public static function bySlug(string $slug): ?array
    {
        return self::firstWhere('slug', $slug);
    }
}

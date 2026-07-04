<?php
namespace App\Models;

class Template extends Model
{
    protected static string $table = 'templates';

    public static function active(): array
    {
        return self::where('is_active', 1, 'category, name');
    }
}

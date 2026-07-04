<?php
namespace App\Core;

use App\Models\Setting;

/**
 * Minimal mailer. Uses PHP mail() by default (works on most cPanel hosts).
 * To use SMTP/PHPMailer, drop the library in /app/Lib and swap send().
 */
class Mailer
{
    public static function send(string $to, string $subject, string $body, bool $html = false): bool
    {
        $fromName  = Setting::get('site_name', 'Website');
        $fromEmail = Setting::get('contact_email', 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        $headers   = [];
        $headers[] = 'From: ' . self::encodeName($fromName) . ' <' . $fromEmail . '>';
        $headers[] = 'Reply-To: ' . $fromEmail;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: ' . ($html ? 'text/html' : 'text/plain') . '; charset=UTF-8';

        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
    }

    private static function encodeName(string $name): string
    {
        return '=?UTF-8?B?' . base64_encode($name) . '?=';
    }
}

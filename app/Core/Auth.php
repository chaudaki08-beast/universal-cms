<?php
namespace App\Core;

use App\Models\User;

/**
 * Authentication + role-based access control.
 *
 * Roles (hierarchy): super_admin > editor > content_manager
 * Permissions are derived from role; super_admin can do everything.
 */
class Auth
{
    public const ROLES = ['super_admin', 'editor', 'content_manager'];

    /** Capability matrix: role => list of permission keys ('*' = all). */
    public const ABILITIES = [
        'super_admin'     => ['*'],
        'editor'          => ['pages.edit', 'pages.manage', 'posts.manage', 'media.manage', 'forms.view', 'menus.manage', 'products.manage'],
        'content_manager' => ['pages.edit', 'posts.manage', 'media.manage', 'forms.view'],
    ];

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        if (!$user || (int)$user['is_active'] !== 1) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Transparent re-hash if algorithm/cost changed.
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            User::updatePassword((int)$user['id'], $password);
        }

        session_regenerate_id(true);
        Session::set('user_id', (int) $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);
        User::touchLogin((int) $user['id']);
        return true;
    }

    public static function logout(): void
    {
        Session::forget('user_id');
        Session::forget('user_role');
        Session::forget('user_name');
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function user(): ?array
    {
        $id = self::id();
        return $id ? User::find($id) : null;
    }

    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    public static function can(string $ability): bool
    {
        $role = self::role();
        if (!$role) return false;
        $abilities = self::ABILITIES[$role] ?? [];
        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }

    /** Guard: require login, else redirect to admin login. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::set('_intended', $_SERVER['REQUEST_URI'] ?? admin_url());
            redirect('admin/login');
        }
    }

    /** Guard: require a specific ability, else 403. */
    public static function requireAbility(string $ability): void
    {
        self::requireLogin();
        if (!self::can($ability)) {
            http_response_code(403);
            exit('403 — You do not have permission to perform this action.');
        }
    }

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

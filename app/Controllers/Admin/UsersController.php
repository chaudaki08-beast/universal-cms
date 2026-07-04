<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Models\User;

class UsersController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('*'); // super_admin only
    }

    public function index(): void
    {
        $users = User::all('id');
        $this->adminView('admin/users/index', compact('users'), 'Users & Roles');
    }

    public function create(): void
    {
        $user = null;
        $this->adminView('admin/users/edit', compact('user'), 'New User');
    }

    public function edit(string $id): void
    {
        $user = User::find((int) $id);
        if (!$user) { redirect('admin/users'); }
        $this->adminView('admin/users/edit', compact('user'), 'Edit User');
    }

    public function store(): void
    {
        $id    = Request::int('id') ?: null;
        $name  = Request::str('name');
        $email = Request::str('email');
        $role  = Request::str('role', 'content_manager');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Valid email required.'); $this->back();
        }
        if (!in_array($role, Auth::ROLES, true)) { $role = 'content_manager'; }

        $existing = User::findByEmail($email);
        if ($existing && (int) $existing['id'] !== (int) $id) {
            Session::flash('error', 'That email is already in use.'); $this->back();
        }

        $data = [
            'name' => $name, 'email' => $email, 'role' => $role,
            'is_active' => Request::bool('is_active') ? 1 : 0,
        ];
        $pass = Request::str('password');
        if ($pass !== '') {
            if (strlen($pass) < 8) { Session::flash('error', 'Password must be 8+ characters.'); $this->back(); }
            $data['password'] = Auth::hash($pass);
        }

        if ($id) {
            User::updateById($id, $data);
        } else {
            if ($pass === '') { Session::flash('error', 'Password is required for new users.'); $this->back(); }
            User::create($data);
        }
        Session::flash('success', 'User saved.');
        redirect('admin/users');
    }

    public function destroy(string $id): void
    {
        if ((int) $id === Auth::id()) {
            Session::flash('error', 'You cannot delete your own account.');
            redirect('admin/users');
        }
        User::deleteById((int) $id);
        Session::flash('success', 'User deleted.');
        redirect('admin/users');
    }
}

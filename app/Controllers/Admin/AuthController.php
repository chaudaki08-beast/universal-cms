<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) { redirect('admin'); }

        if (Request::isPost()) {
            Csrf::verify();
            $email = Request::str('email');
            $pass  = Request::str('password');

            // Basic brute-force throttle (per session)
            $attempts = (int) Session::get('_login_attempts', 0);
            if ($attempts >= 6) {
                Session::flash('error', 'Too many attempts. Please wait a moment and try again.');
                redirect('admin/login');
            }

            if (Auth::attempt($email, $pass)) {
                Session::forget('_login_attempts');
                $intended = Session::get('_intended', admin_url());
                Session::forget('_intended');
                header('Location: ' . $intended);
                exit;
            }
            Session::set('_login_attempts', $attempts + 1);
            Session::flash('error', 'Invalid email or password.');
            redirect('admin/login');
        }

        \App\Core\View::render('admin/auth/login', [
            'flashes' => Session::getFlashes(),
            'pageTitle' => 'Sign in',
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'You have been signed out.');
        redirect('admin/login');
    }
}

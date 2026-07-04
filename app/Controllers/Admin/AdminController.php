<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;

/**
 * Base for every authenticated admin controller.
 * Enforces login on construct and CSRF on write requests.
 */
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
        if (Request::method() !== 'GET') {
            Csrf::verify();
        }
    }
}

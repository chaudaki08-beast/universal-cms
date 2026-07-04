<?php
namespace App\Core;

/** Base controller: shared view/json/redirect helpers + guards. */
abstract class Controller
{
    /** Render an admin view inside the admin layout. */
    protected function adminView(string $view, array $data = [], string $title = 'Dashboard'): void
    {
        $data['pageTitle'] = $title;
        $data['flashes']   = Session::getFlashes();
        View::render($view, $data, 'admin');
    }

    /** Render a front-end view inside the public layout. */
    protected function frontView(string $view, array $data = []): void
    {
        $data['flashes'] = Session::getFlashes();
        View::render($view, $data, 'front');
    }

    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function back(): void
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? admin_url();
        header('Location: ' . $ref);
        exit;
    }

    /** Persist current POST data so forms can repopulate on error. */
    protected function withOld(): void
    {
        Session::set('_old', $_POST);
    }
}

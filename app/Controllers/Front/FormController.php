<?php
namespace App\Controllers\Front;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;
use App\Core\Mailer;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Setting;

class FormController extends FrontController
{
    public function submit(): void
    {
        Csrf::verify();

        // Honeypot anti-spam: bots fill hidden "website" field.
        if (Request::str('website') !== '') {
            redirect(Request::str('_redirect', '/'));
        }

        $slug = Request::str('form_slug', 'contact');
        $form = Form::bySlug($slug);
        if (!$form) {
            Session::flash('error', 'Form not found.');
            redirect(Request::str('_redirect', '/'));
        }

        $fields = json_field($form['fields']);
        $values = [];
        $errors = [];

        foreach ($fields as $f) {
            $val = Request::str('field_' . $f['name']);
            if (!empty($f['required']) && $val === '') {
                $errors[] = $f['label'] . ' is required.';
            }
            if (($f['type'] ?? '') === 'email' && $val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $f['label'] . ' must be a valid email.';
            }
            $values[$f['label']] = $val;
        }

        if ($errors) {
            Session::flash('error', implode(' ', $errors));
            redirect(Request::str('_redirect', '/'));
        }

        // Store submission
        FormEntry::create([
            'form_id' => (int) $form['id'],
            'data'    => json_encode($values),
            'ip'      => Request::ip(),
        ]);

        // Email notification
        $to = $form['notify_email'] ?: Setting::get('contact_email', '');
        if ($to) {
            $body = "New submission for \"{$form['name']}\":\n\n";
            foreach ($values as $label => $val) { $body .= "$label: $val\n"; }
            Mailer::send($to, 'New form submission: ' . $form['name'], $body);
        }

        Session::flash('success', $form['success_message'] ?: 'Thank you! Your submission has been received.');
        redirect(Request::str('_redirect', '/'));
    }
}

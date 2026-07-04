<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Models\Form;
use App\Models\FormEntry;

class FormsController extends AdminController
{
    public function index(): void
    {
        $forms = Database::all(
            "SELECT f.*, (SELECT COUNT(*) FROM form_entries e WHERE e.form_id=f.id) AS entry_count
             FROM forms f ORDER BY f.created_at DESC"
        );
        $this->adminView('admin/forms/index', compact('forms'), 'Forms');
    }

    public function create(): void
    {
        Auth::requireAbility('*');
        $form = null;
        $this->adminView('admin/forms/edit', compact('form'), 'New Form');
    }

    public function edit(string $id): void
    {
        Auth::requireAbility('*');
        $form = Form::find((int) $id);
        if (!$form) { redirect('admin/forms'); }
        $this->adminView('admin/forms/edit', compact('form'), 'Edit Form');
    }

    public function store(): void
    {
        Auth::requireAbility('*');
        $id = Request::int('id') ?: null;
        $name = Request::str('name', 'Form');

        // Build field definitions from parallel arrays
        $labels   = Request::array('field_label');
        $types    = Request::array('field_type');
        $required = Request::array('field_required');
        $fields = [];
        foreach ($labels as $i => $label) {
            if (trim($label) === '') continue;
            $fields[] = [
                'name'     => slugify($label) . '_' . $i,
                'label'    => $label,
                'type'     => $types[$i] ?? 'text',
                'required' => !empty($required[$i]),
            ];
        }

        $data = [
            'name'   => $name,
            'slug'   => Request::str('slug') ?: slugify($name),
            'fields' => json_encode($fields),
            'notify_email' => Request::str('notify_email'),
            'success_message' => Request::str('success_message', 'Thank you!'),
        ];

        if ($id) { Form::updateById($id, $data); }
        else { $id = Form::create($data); }

        Session::flash('success', 'Form saved.');
        redirect('admin/forms/edit/' . $id);
    }

    public function entries(string $id): void
    {
        $form = Form::find((int) $id);
        if (!$form) { redirect('admin/forms'); }
        $entries = FormEntry::forForm((int) $id);
        Database::run("UPDATE form_entries SET is_read=1 WHERE form_id=?", [(int) $id]);
        $this->adminView('admin/forms/entries', compact('form', 'entries'), 'Entries: ' . $form['name']);
    }

    public function destroy(string $id): void
    {
        Auth::requireAbility('*');
        Form::deleteById((int) $id);
        Session::flash('success', 'Form deleted.');
        redirect('admin/forms');
    }
}

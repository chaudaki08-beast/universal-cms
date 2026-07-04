<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Upload;
use App\Models\Setting;
use App\Models\Media;

class SettingsController extends AdminController
{
    public function index(): void
    {
        Auth::requireAbility('*');
        $tab = Request::str('tab', 'general');
        $settings = [
            'general' => Setting::group('general'),
            'theme'   => Setting::group('theme'),
            'header'  => Setting::group('header'),
            'footer'  => Setting::group('footer'),
            'seo'     => Setting::group('seo'),
            'social'  => Setting::group('social'),
        ];
        $this->adminView('admin/settings/index', compact('settings', 'tab'), 'Settings');
    }

    public function save(): void
    {
        Auth::requireAbility('*');
        $group  = Request::str('group', 'general');
        $fields = Request::array('settings');

        foreach ($fields as $key => $value) {
            Setting::set($key, is_array($value) ? json_encode($value) : (string) $value, $group);
        }

        // Handle logo / favicon / og_image uploads if present
        foreach (['logo', 'favicon', 'og_image'] as $imgKey) {
            $file = Request::file($imgKey);
            if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $res = Upload::handle($file, 'branding');
                if ($res['success']) {
                    Setting::set($imgKey, $res['path'], $imgKey === 'og_image' ? 'seo' : 'general');
                    Media::create([
                        'folder' => '/branding', 'name' => $res['name'], 'path' => $res['path'],
                        'mime' => $res['mime'], 'size' => $res['size'], 'uploaded_by' => Auth::id(),
                    ]);
                } else {
                    Session::flash('error', $res['error']);
                }
            }
        }

        Session::flash('success', 'Settings saved.');
        redirect('admin/settings/index?tab=' . $group);
    }
}

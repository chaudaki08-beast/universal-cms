<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Upload;
use App\Core\Database;
use App\Models\Media;

class MediaController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('media.manage');
    }

    public function index(): void
    {
        $q       = Request::str('q');
        $folder  = Request::str('folder');
        $items   = Media::search($q, $folder);
        $folders = Media::folders();
        $this->adminView('admin/media/index', compact('items', 'folders', 'q', 'folder'), 'Media Library');
    }

    public function upload(): void
    {
        $folder = Request::str('folder', '/') ?: '/';
        $files  = $_FILES['files'] ?? null;
        $count  = 0;

        if ($files && is_array($files['name'])) {
            foreach ($files['name'] as $i => $name) {
                $one = [
                    'name' => $files['name'][$i], 'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];
                if ($one['error'] === UPLOAD_ERR_NO_FILE) continue;
                $sub = trim($folder, '/') ?: date('Y/m');
                $res = Upload::handle($one, $sub);
                if ($res['success']) {
                    Media::create([
                        'folder' => '/' . trim($folder, '/'), 'name' => $res['name'],
                        'path' => $res['path'], 'mime' => $res['mime'], 'size' => $res['size'],
                        'uploaded_by' => Auth::id(),
                    ]);
                    $count++;
                } else {
                    Session::flash('error', $res['name'] ?? 'A file' . ': ' . $res['error']);
                }
            }
        }

        if (Request::isAjax()) {
            $this->json(['ok' => true, 'uploaded' => $count, 'items' => Media::search('', '')]);
        }
        Session::flash('success', "$count file(s) uploaded.");
        redirect('admin/media');
    }

    public function rename(string $id): void
    {
        Database::update('media', ['name' => Request::str('name')], ['id' => (int) $id]);
        Session::flash('success', 'File renamed.');
        $this->back();
    }

    public function destroy(string $id): void
    {
        $item = Media::find((int) $id);
        if ($item) {
            $abs = UPLOADS_PATH . '/' . $item['path'];
            if (is_file($abs)) { @unlink($abs); }
            Media::deleteById((int) $id);
        }
        if (Request::isAjax()) { $this->json(['ok' => true]); }
        Session::flash('success', 'File deleted.');
        redirect('admin/media');
    }

    /** JSON list for the media picker modal used across the admin. */
    public function picker(): void
    {
        $items = Media::search(Request::str('q'), '');
        $this->json(['items' => array_map(fn($m) => [
            'id' => $m['id'], 'name' => $m['name'],
            'url' => uploads_url($m['path']), 'path' => $m['path'], 'mime' => $m['mime'],
        ], $items)]);
    }
}

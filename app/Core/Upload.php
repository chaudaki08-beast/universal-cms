<?php
namespace App\Core;

/**
 * Secure file uploads for the Media Library.
 * - Validates MIME via finfo (not just extension)
 * - Whitelists extensions
 * - Randomises stored filename (prevents overwrite + path tricks)
 * - Stores under /uploads/YYYY/MM
 */
class Upload
{
    public const IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    public const DOC_EXT   = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

    public const MIME_MAP = [
        'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif'  => 'image/gif',  'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'pdf'  => 'application/pdf',
        'txt'  => 'text/plain', 'csv' => 'text/plain',
    ];

    /**
     * @return array{success:bool,error?:string,path?:string,url?:string,name?:string,mime?:string,size?:int}
     */
    public static function handle(array $file, ?string $subfolder = null): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Invalid upload.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload failed (code ' . $file['error'] . ').'];
        }

        $maxBytes = 16 * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            return ['success' => false, 'error' => 'File exceeds 16MB limit.'];
        }

        $original = $file['name'];
        $ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed  = array_merge(self::IMAGE_EXT, self::DOC_EXT);

        if (!in_array($ext, $allowed, true)) {
            return ['success' => false, 'error' => 'File type .' . $ext . ' is not allowed.'];
        }

        // Verify real MIME (skip for svg/txt/csv which finfo treats loosely)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']) ?: 'application/octet-stream';
        finfo_close($finfo);

        if (isset(self::MIME_MAP[$ext]) && !in_array($ext, ['svg', 'txt', 'csv'], true)) {
            if ($mime !== self::MIME_MAP[$ext]) {
                return ['success' => false, 'error' => 'File content does not match its extension.'];
            }
        }

        // Build destination
        $sub  = $subfolder ? trim($subfolder, '/') : date('Y/m');
        $dir  = UPLOADS_PATH . '/' . $sub;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return ['success' => false, 'error' => 'Could not create upload directory.'];
        }

        $safeBase = preg_replace('/[^a-z0-9_-]/', '-', strtolower(pathinfo($original, PATHINFO_FILENAME)));
        $safeBase = substr(trim($safeBase, '-'), 0, 60) ?: 'file';
        $filename = $safeBase . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest     = $dir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Could not save uploaded file.'];
        }
        @chmod($dest, 0644);

        $relative = $sub . '/' . $filename;
        return [
            'success' => true,
            'path'    => $relative,
            'url'     => uploads_url($relative),
            'name'    => $original,
            'mime'    => $mime,
            'size'    => (int) $file['size'],
        ];
    }
}

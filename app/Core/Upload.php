<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Upload
{
    private const ALLOWED = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public static function image(string $field, ?string $current = null): ?string
    {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $current;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('A imagem deve ter no máximo 5 MB.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            throw new RuntimeException('Use uma imagem JPG, PNG ou WebP.');
        }
        $directory = PUBLIC_PATH . '/uploads';
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Não foi possível preparar a pasta de uploads.');
        }
        $name = bin2hex(random_bytes(16)) . '.' . self::ALLOWED[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
            throw new RuntimeException('Não foi possível salvar a imagem.');
        }
        return 'uploads/' . $name;
    }

    public static function images(string $field): array
    {
        $files = $_FILES[$field] ?? null;
        if (!$files || !is_array($files['name'] ?? null)) {
            return [];
        }
        $paths = [];
        foreach ($files['name'] as $index => $originalName) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $_FILES['_gallery_image'] = [
                'name' => $originalName,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
            $path = self::image('_gallery_image');
            if ($path) {
                $paths[] = $path;
            }
        }
        unset($_FILES['_gallery_image']);
        return $paths;
    }
}


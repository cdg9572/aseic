<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use ZipArchive;

class PublishingOriginalAssetController extends Controller
{
    public function __invoke(string $path): Response
    {
        abort_if($path === '' || str_contains($path, '..') || str_contains($path, '\\') || str_contains($path, "\0"), 404);
        abort_unless(
            str_starts_with($path, 'css/') || str_starts_with($path, 'js/') || str_starts_with($path, 'images/'),
            404
        );

        $contentType = $this->contentType($path);
        abort_if($contentType === null, 404);

        $archivePath = base_path('docs/aseic.zip');
        abort_unless(is_file($archivePath), 404);

        $zip = new ZipArchive;
        abort_unless($zip->open($archivePath) === true, 404);

        $entry = 'public/'.ltrim($path, '/');
        $contents = $zip->getFromName($entry);
        $zip->close();

        abort_if($contents === false, 404);

        if (str_ends_with(strtolower($path), '.css')) {
            $contents = str_replace(
                ["url('/images/", 'url("/images/', "url('/css/", 'url("/css/'],
                ["url('/publishing-original-assets/images/", 'url("/publishing-original-assets/images/', "url('/publishing-original-assets/css/", 'url("/publishing-original-assets/css/'],
                $contents
            );
        }

        return response($contents, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function contentType(string $path): ?string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'avif' => 'image/avif',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            default => null,
        };
    }
}

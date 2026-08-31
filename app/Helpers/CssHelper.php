<?php

namespace App\Helpers;

class CssHelper
{
    public static function minify($css)
    {
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([\{\}\:\;\,])\s*/', '$1', $css);
        $css = str_replace(';}', '}', $css);
        return trim($css);
    }

    public static function minTag($srcPath, $media = 'all')
    {
        // 라라벨에서는 DOCUMENT_ROOT 대신 public_path() 사용
        $srcFile = public_path($srcPath);

        if (!file_exists($srcFile)) {
            return '';
        }

        $minDir = public_path('css/min');

        if (!is_dir($minDir)) {
            mkdir($minDir, 0755, true);
        }

        $minName = str_replace('/', '_', ltrim($srcPath, '/'));
        $minName = preg_replace('/\.css$/', '.min.css', $minName);
        $minFile = $minDir . '/' . $minName;
        $minUrl = asset('css/min/' . $minName);

        if (!file_exists($minFile) || filemtime($srcFile) > filemtime($minFile)) {
            $lockDir = storage_path('framework/cache');
            $lockFile = $lockDir . '/css-min-' . sha1($minFile) . '.lock';

            if (!is_dir($lockDir)) {
                mkdir($lockDir, 0755, true);
            }

            $fp = fopen($lockFile, 'c+');
            if (flock($fp, LOCK_EX)) {
                if (!file_exists($minFile) || filemtime($srcFile) > filemtime($minFile)) {
                    $css = file_get_contents($srcFile);
                    file_put_contents($minFile, self::minify($css));
                }
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

        $ver = filemtime($minFile);
        return '<link rel="stylesheet" href="' . $minUrl . '?v=' . $ver . '" media="' . $media . '">';
    }
}

<?php

namespace YOOtheme\Widgetkit\Controller;

use YOOtheme\Framework\Routing\Controller;
use YOOtheme\Framework\Routing\Exception\HttpException;

class CacheController extends Controller
{
    private function getFiles()
    {
        $directory = new \RecursiveDirectoryIterator(
            $this->app['path.cache'] . '/',
            \FilesystemIterator::FOLLOW_SYMLINKS
        );
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = [];

        foreach ($iterator as $info) {
            if (
                $info->isFile() &&
                strpos($info->getRealPath(), 'fonts' . DIRECTORY_SEPARATOR) == false
            ) {
                $files[] = $info->getRealPath();
            }
        }

        return $files;
    }

    public function getCacheSizeAction()
    {
        $sizes = [' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
        $size = 0;

        foreach ($this->getFiles() as $file) {
            $size += filesize($file);
        }

        $size =
            $size == 0
                ? '0 KB'
                : round($size / pow(1024, $i = floor(log($size, 1024))), 2) . $sizes[$i];

        return $this['response']->json(json_encode("{$size} in cache"));
    }

    public function clearCacheAction()
    {
        foreach ($this->getFiles() as $file) {
            unlink($file);
        }

        return $this['response']->json(json_encode('cache cleared'));
    }

    public static function getRoutes()
    {
        return [
            ['/cache/get', 'getCacheSizeAction', 'GET', ['access' => 'manage_widgetkit']],
            ['/cache/clear', 'clearCacheAction', 'GET', ['access' => 'manage_widgetkit']],
        ];
    }
}

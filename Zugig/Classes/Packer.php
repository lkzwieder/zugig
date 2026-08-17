<?php

class Packer
{
    private string $root;

    public function __construct(?string $root = null)
    {
        $this->root = $root ?? (defined('ROOT') ? ROOT : __DIR__);
    }

    public function pack(array $items, string $type = 'js', bool $minify = true, ?string $cachePath = null): string
    {
        $cachePath ??= sys_get_temp_dir() . '/zugig';
        $content = $this->assemble($items, $type);
        $hash = sha1($content);
        $cacheDir = $cachePath . '/packed';
        $cacheFile = "{$cacheDir}/{$hash}.{$type}";

        if (file_exists($cacheFile)) {
            return $cacheFile;
        }

        $packed = $minify
            ? $this->minify($content, $type)
            : $content;

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        file_put_contents($cacheFile, $packed);
        return $cacheFile;
    }

    private function assemble(array $items, string $type): string
    {
        $content = '';

        foreach ($items as $item) {
            $typeKey = $item['type'] ?? 'code';
            $data = $item['data'] ?? '';

            if ($typeKey === 'code') {
                $content .= strip_tags($data);
            } elseif ($typeKey === 'file') {
                $filePath = str_starts_with($data, 'http')
                    ? $data
                    : $this->root . DIRECTORY_SEPARATOR . $data;
                $content .= file_get_contents($filePath);
            }

            $content .= "\n";
        }

        return trim($content);
    }

    private function minify(string $content, string $type): string
    {
        if ($type === 'css') {
            return MinifierCSS::minify($content);
        }

        return \JShrink\Minifier::minify($content);
    }
}

// Uso:
// $packer = new Packer(ROOT);
// $file = $packer->pack([
//     ['type' => 'file', 'data' => 'js/base.js'],
//     ['type' => 'code', 'data' => 'console.log("app")'],
// ], 'js');
// // returns path to packed file in sys_get_temp_dir()

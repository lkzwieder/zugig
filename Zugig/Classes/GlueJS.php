<?php

class GlueJS extends Glue
{
    public function tag(array $options = []): string
    {
        $minified = $this->minify();
        $type = $options['type'] ?? 'text/javascript';
        return "<script type=\"{$type}\">{$minified}</script>";
    }

    public function tagLink(string $src, array $options = []): string
    {
        $attrs = ['src' => $src];
        if (isset($options['type'])) $attrs['type'] = $options['type'];
        if (isset($options['defer'])) $attrs['defer'] = 'defer';
        if (isset($options['async'])) $attrs['async'] = 'async';
        $parts = array_map(fn($k, $v) => htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"', array_keys($attrs), $attrs);
        return '<script ' . implode(' ', $parts) . '></script>';
    }

    protected function minify(): string
    {
        return defined('JS_MINIFIER') && JS_MINIFIER
            ? \JShrink\Minifier::minify($this->flush())
            : $this->flush();
    }
}

// Uso:
// $js = new GlueJS();
// $js->addFile('js/base.js')
//    ->addFile('js/utils.js')
//    ->addFile('js/app.js')
//    ->tag(); // returns <script>...</script>

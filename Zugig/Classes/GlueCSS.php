<?php

class GlueCSS extends Glue
{
    public function tag(array $options = []): string
    {
        $minified = $this->minify();
        $type = $options['type'] ?? 'text/css';
        return "<style type=\"{$type}\">{$minified}</style>";
    }

    public function tagLink(string $href, array $options = []): string
    {
        $rel = $options['rel'] ?? 'stylesheet';
        $type = $options['type'] ?? 'text/css';
        $media = $options['media'] ?? 'all';
        $attrs = array_map(fn($k, $v) => htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"', array_keys($options), $options);
        $extra = empty($attrs) ? '' : ' ' . implode(' ', $attrs);
        return "<link rel=\"{$rel}\" href=\"{$href}\" type=\"{$type}\" media=\"{$media}\"{$extra}>";
    }

    protected function minify(): string
    {
        return defined('CSS_MINIFIER') && CSS_MINIFIER
            ? MinifierCSS::minify($this->flush())
            : $this->flush();
    }
}

// Uso:
// $css = new GlueCSS();
// $css->addFile('css/base.css')
//     ->addFile('css/layout.css')
//     ->addFile('css/theme.css')
//     ->tag(); // returns <style>...</style>

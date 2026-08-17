<?php

class MinifierCSS
{
    public static function minify(string $css): string
    {
        $css = preg_replace('@/\*[^*]*\*+([^/][^*]*\*+)*/@', '', $css);

        $css = str_replace(
            ["\r", "\n", "\t", " {", "} ", ";}", "; ", "; ", " ;", ": ", " :"],
            ['', '', '', '{', '}', ';', ';', ';', ';', ':', ':'],
            $css
        );

        return preg_replace(
            ['@\s\s+@', '@(\w+:)\s*([\w\s,#]+;?)@', '@([\{,])\s+@', '@\s+([\}])@'],
            [' ', '$1$2', '$1 ', ' $1'],
            $css
        );
    }
}

// Uso:
// $minified = MinifierCSS::minify($cssContent);

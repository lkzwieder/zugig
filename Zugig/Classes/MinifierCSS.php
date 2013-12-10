<?php
class MinifierCSS {
    public static function minify($data) {
        return preg_replace(
            ['@\s\s+@','@(\w+:)\s*([\w\s,#]+;?)@'],
            [' ','$1$2'],
            str_replace(
                ["\r","\n","\t",' {','} ',';}','; '],
                ['','','','{','}','}',';'],
                preg_replace('@/\*[^*]*\*+([^/][^*]*\*+)*/@', '', $data)
            )
        );
    }
}
<?php
class GlueJS extends Glue {
    protected static $instance = null;

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function print_tag() {?>
        <script type="text/javascript"><?=$this->flush()?></script>
    <?php }

    public function print_url_tag() {
        #TODO complete print_url_tag
    }

    protected function __construct() {}

    protected function minify() {
        return JShrink\Minifier::minify($this->get_packed_data());
    }

    protected function flush() {
        return JS_MINIFIER ? $this->minify() : $this->get_packed_data();
    }
}
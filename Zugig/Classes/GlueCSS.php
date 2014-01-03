<?php
class GlueCSS extends Glue {
    protected static $instance = null;

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {}

    public function minify() {
        return MinifierCSS::minify($this->get_packed_data());
    }
}
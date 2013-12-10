<?php
class GlueJS implements Glue {
    private static $instance = null;
    private $dependencies;

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set_chunk() {}
    public function get_data() {}
    public function print_data() {}
    public function print_route() {}

    private function __construct() {
        $this->dependencies = new Dependencies();
    }

    private function pack() {}
    private function minify() {}
    private function version_file() {}
}
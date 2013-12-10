<?php
abstract class Glue {
    protected  $dependencies;
    protected static $instance = null;

    protected function __construct() {
        $this->dependencies = new Dependencies();
    }

    public function get_clean_data() {
        $res = $this->get_data();
        self::$instance = null;
        return $res;
    }

    public abstract function set_raw_data($raw, $name = false, Array $need = []);
    public abstract function set_tag_data($tag, $name = false, Array $need = []);
    public abstract function set_url_data($url, $name = false, Array $need = []);

    public abstract function get_data();
    public abstract function print_data();
    public abstract function print_route();
}
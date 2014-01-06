<?php
class Autoload {
    private $directories = array();
    private static $instance = false;

    public static function get_instance() {
        if(!self::$instance) {
            $class = get_called_class();
            self::$instance = new $class();
        }
        return self::$instance;
    }

    public function set_path($path) {
        $this->directories[] = $path;
    }

    private function __construct() {
        $this->set_path(ROOT);
        $this->set_path(APP_ROOT);
        spl_autoload_register(array($this, 'autoload'));
    }

    private function autoload($className) {
        $pathFile = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $className).'.php';
        $toRequire = false;
        foreach($this->directories as $dir) {
            if($toRequire = realpath($dir.DIRECTORY_SEPARATOR.$pathFile)) break;
        }
        if($toRequire) require $toRequire;
    }
}
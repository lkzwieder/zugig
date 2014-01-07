<?php
class Config {
    protected static $instance = [];
    protected $data;

    public static function get_instance($key, $file_path = null, $mode = true) {
        if($file_path) {
            self::$instance[$key] = new self($file_path, $mode);
        } elseif(!isset(self::$instance[$key])) {
            throw new Exception("Are you a developer?...");
        }
        return self::$instance[$key];
    }

    public function get_config() {
        return (object) $this->data;
    }

    protected function __construct($file_path, $mode) {
        $this->data = parse_ini_file($file_path, $mode);
    }
}
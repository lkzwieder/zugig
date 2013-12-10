<?php
class Connect {
    private static $instance = [];

    public static function get_instance($env) {
        if(!self::$instance[$env]) {
            self::$instance = new self($env);
        }
        return self::$instance;
    }

    private function __construct() {

    }
}
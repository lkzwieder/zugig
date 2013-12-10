<?php
class Database {
    private static $instance = [];

    public static function get_instance($db_name) {
        if(!isset(self::$instance[$db_name])) {
            $settings = parse_ini_file(DATABASES, true);
            if($settings[$db_name]['driver'] == "dblib") {
                $dns = $settings[$db_name]['driver'].":host=".$settings[$db_name]['host'].":".$settings[$db_name]['port'].
                    ";dbname=".$settings[$db_name]['dbname'];
            } else {
                $dns = $settings[$db_name]['driver'] .
                    ':host=' . $settings[$db_name]['host'] .
                    ((!empty($settings[$db_name]['port'])) ? (';port=' . $settings[$db_name]['port']) : '') .
                    ';dbname=' . $settings[$db_name]['dbname'];
            }

            try {
                $pdo = new PDO($dns, $settings[$db_name]['username'], $settings[$db_name]['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance[$db_name] = $pdo;
            } catch(PDOException $e) {
                echo 'Connection failed: '.$e->getMessage()."\n";
            }
        }
        return self::$instance[$db_name];
    }

    public static function close($db_name) {
        self::$instance[$db_name] = null;
    }

    private function __construct() {

    }

    public static function get_bulk_insert($table, Array $fields, Array $data, $replace = false, $quantity = 10000) {
        $query = array();
        $fields = static::array_to_values($fields);
        $d = static::multiple_unshift($data, 0, count($data) <= $quantity ? count($data) : count($data) % $quantity);
        $data = $d['origin'];
        $query[] = static::insert_maker($table, $fields, $d['shifted'], $replace);
        while($data) {
            $d = static::multiple_unshift($data, 0, $quantity);
            $data = $d['origin'];
            $query[] = static::insert_maker($table, $fields, $d['shifted'], $replace);
        }
        return $query;
    }

    public static function get_bulk_update($q, Array $data, $quantity = 3000) {
        $query = array();
        $d = static::multiple_unshift($data, 0, count($data) <= $quantity ? count($data) : count($data) % $quantity);
        $data = $d['origin'];
        $query[] = sprintf($q, static::array_to_values($d['shifted']));
        while($data) {
            $d = static::multiple_unshift($data, 0, $quantity);
            $data = $d['origin'];
            $query[] = sprintf($q, static::array_to_values($d['shifted']));
        }
        return $query;
    }

    public static function multiple_unshift(Array $array, $from, $quantity) {
        $shifted = array_splice($array, $from, $quantity);
        return array("origin" => $array, "shifted" => $shifted);
    }

    private static function insert_maker($table, $fields, $data, $replace) {
        $query = $replace ? "REPLACE" : "INSERT";
        $query .= " INTO ".$table." ".$fields. " VALUES ";
        $values = [];
        foreach($data as $v) {
            $values[] = static::array_to_values(array_map('json_encode', $v));
        }
        return $query. implode(", ", $values);
    }

    public static function array_to_values($data) {
        $srt = "(".implode(", ", $data).")";
        return $srt;
    }
}
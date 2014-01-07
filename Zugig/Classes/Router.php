<?php
class Router {
    private $request_uri;
    private $routes;
    private static $instance = false;

    public static function get_instance() {
        if(!self::$instance) {
            $class = get_called_class();
            self::$instance = new $class();
        }
        return self::$instance;
    }

    protected function __construct() {
        list($this->request_uri) = explode('?', $_SERVER['REQUEST_URI']);
    }

    public function set_route($route, $target = [], $condition = []) {
        $url_regex = preg_replace_callback('@:[\w]+@', function($matches) use ($condition) {
            $res = '([a-zA-Z0-9_\+\-%]+)';
            $key = str_replace(':', '', $matches[0]);
            if(array_key_exists($key, $condition)) $res = '('.$condition[$key].')';
            return $res;
        }, $route);
        preg_match_all('@:([\w]+)@', $route, $keys, PREG_PATTERN_ORDER);
        $this->routes[$route] = ['regex' => $url_regex .'/?', 'target' => $target, 'keys' => $keys[0]];
    }

    public function run() {
        $params = [];
        foreach($this->routes as $v) {
            if(preg_match('@^'.$v['regex'].'$@', $this->request_uri, $values)) {
                if(isset($v['target']['controller'])) $controller = $v['target']['controller'];
                if(isset($v['target']['action'])) $action =  $v['target']['action'];
                for($i = count($values) -1; $i--;) {
                    $params[substr($v['keys'][$i], 1)] = $values[$i +1];
                }
                break;
            }
        }
        $controller = isset($controller) ? $controller : DEFAULT_CONTROLLER;
        $action = isset($action) ? $action : DEFAULT_ACTION;
        $redirect = new $controller();
        $redirect->{$action}(array_merge($params, $_GET, $_POST, $_COOKIE));
    }
}
<?php
class Controller {
    private $vars = [];
    private $view;

    public function __construct() {
        $this->set_view(substr(get_called_class(), 12)); # controllers
    }

    public function set_vars(Array $args) {
        $this->vars = $args;
    }

    public function render($filename = false) {
        if($filename) $this->set_view($filename);
        $view = (object) $this->vars;
        require $this->view.'.php';
    }

    public function set_view($filename) {
        $this->view = path(APP_ROOT, "Views", strtolower($filename));
    }
} 
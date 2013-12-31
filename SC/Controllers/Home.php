<?php
class Controllers_Home extends Controller {
    public function index() {
        $this->set_vars(['title' => 'Scholar beta']);
        $this->render();
    }

    public function home($args) {
        echo "<pre>";
        var_dump($args);
    }
}
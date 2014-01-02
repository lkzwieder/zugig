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

    public function test_dependencies() {
        $d = new Dependencies();
        $d->add_data("segundo", "segundo", ["primero"]);
        $d->add_data("quinto", "quinto", ["primero", "cuarto"]);
        $d->add_data("primero", "primero");
        $d->add_data("cuarto", "cuarto", ["tercero"]);
        $d->add_data("tercero", "tercero", ["segundo"]);
        echo "<pre>";var_dump($d->get_data());
    }
}
<?php
class Controllers_Home extends Controller {
    public function index() {
        $this->set_vars(['title' => 'Scholar beta']);
        $this->render();
    }
}
<?php
class Controllers_Spa {
    public function index() {
        echo file_get_contents(path(APP_ROOT, 'public', 'index.html'));
    }
}
<?php
class TestController extends Controller {
    public function assert($var1, $var2, $msg = "Salio joya") {
        return $var1 === $var2 ? $msg : "Salio para el culo chango";
    }

    public function pre_var_dump() {
        echo "<pre>";var_dump(func_get_args());
    }
}
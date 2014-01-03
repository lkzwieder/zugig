<?php
class Glue extends Dependencies {
    public function begin_tag_data() {
        ob_start();
    }

    public function end_tag_data($name = false, Array $need = []) {
        $this->add_data(['code', ob_get_clean()], $name, $need);
    }

    public function set_url_data($url, $name = false, Array $need = []) {
        $this->add_data(['file', $url], $name, $need);
    }

    public function get_packed_data() {
        $res = "";
        foreach($this->get_data() as $v) {
            list($type, $data) = $v;
            if($type == "code") {
                $d = strip_tags($data);
            } else {
                # TODO this 'else' can be better, lack of time... sorry
                $data = strpos($data, "http") === false ? APP_ROOT.$data : $data;
                $d = file_get_contents($data);
            }
            $res .= trim($d);
        }
        return $res;
    }
}
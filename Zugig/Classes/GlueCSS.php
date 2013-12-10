<?php
class GlueCSS extends Glue {
    const CODE = 1;
    const URL = 2;

    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set_tag_data($tag, $name = false, Array $need = []) {
        // TODO strip the tag with a regex
        $this->set_raw_data($tag, $name, $need);
    }

    public function set_url_data($url, $name = false, Array $need = []) {
        //TODO make www.domain.com like url available
        if(strpos($url, "http") === false) {
            $url = "http://".$_SERVER["HTTP_HOST"].$url;
        }
        return $this->set_raw_data(file_get_contents($url), $name, $need);
    }

    public function set_raw_data($raw, $name = false, Array $need = []) {
        $this->dependencies->add_data(MinifierCSS::minify($raw), $name, $need);
    }

    public function get_data() {
        return $this->dependencies->get_data();
    }

    public function print_data() {}
    public function print_route() {}

    private function get_content($content) {
        $res = "";
        foreach($content as $v) {
            list($type, $data) = $v;
            if($type == "code") {
                $d = strip_tags($data);
            } else {
                if(strpos($data, "http") === false) {
                    $data = "http://".$_SERVER["HTTP_HOST"].$data;
                }
                $d = file_get_contents($data);
            }
            $res .= $d."\n";
        }
        return $res;
    }

    private function pack(Array $data) {
        $p = new Packer();
        return $p->get_path($data);
    }

    private function minify($data) {
        return MinifierCSS::minify($data);
    }
}
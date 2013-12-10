<?php
class Packer {
    private $path;

    public function get_path() {
        return $this->path;
    }

    private function write_file($data, $path) {
        $fp = fopen(ROOT.$path, "w");
        fwrite($fp, $data);
        fclose($fp);
    }

    private function version_file() {

    }
}

class Fwk_Packer {
    private $type;
    public function __construct($type) {
        $this->type = $type;
    }

    public function reduce($data) {
        $packed = $this->pack($data);
        $hash = sha1($packed);
        $cache = Fwk_Cache::getInstance();
        if(!$path = $cache->getCache($hash)) {
            $minified = $this->type == "js" ? $this->minifyJs($packed) : $this->minifyCss($packed);
            $path = $this->getPath($minified);
            $this->writeFile($minified, $path);
            $cache->setCache($hash, $path);
        }
        return $path;
    }

    private function pack($content) {
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

    private function minifyJs($data) {
        return Fwk_Minifier::minify($data);
    }

    private function getPath($data) {
        return TMP_PATH.sha1($data).".".$this->type;
    }

    private function writeFile($data, $path) {
        $fp = fopen(ROOT.$path, "w");
        fwrite($fp, $data);
        fclose($fp);
    }
}
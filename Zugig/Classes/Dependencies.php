<?php
class Dependencies {
    private $queue = [];
    private $executed = [];
    private $nn = 0;
    private $data = [];

    public function get_data() {
        if($this->queue) throw new Exception("The queue has data");
        return $this->data;
    }

    public function add_data($data, $name = false, Array $need = []) {
        return $this->add_code($data, $name, $need);
    }

    private function add_code($data, $name, $need) {
        if(count($need)) {
            foreach($need as $value) {
                $key = array_search($value, $this->executed);
                if($key !== false) {
                    unset($need[$key]);
                }
            }
        }
        return count($need) ? $this->enqueue($data, $name, $need) : $this->insert($data, $name);
    }

    private function insert($data, $name) {
        $this->data[] = $data;
        if($name && false === strpos($name, "_lkz")) {
            $this->set_executed($name);
        }
        return $this->data; #at this point data must have one at least.
    }

    private function enqueue($data, $name, $need) {
        if(!$name) {
            $name = "_lkz".$this->nn++;
        }
        $res = false;
        if(!isset($this->queue[$name])) {
            $this->queue[$name]['data'] = $data;
            $this->queue[$name]['need'] = $need;
            $res = true;
        }
        return $res;
    }

    private function was_dependence($name) {
        foreach($this->queue as $key => $value) {
            $num = array_search($name, $value['need']);
            if($num !== false) {
                unset($this->queue[$key]['need'][$num]);
                if(!count($this->queue[$key]['need'])) {
                    unset($this->queue[$key]);
                    $this->insert($value['data'], $key);
                }
            }
        }
    }

    private function set_executed() {
        foreach(func_get_args() as $name) {
            $this->executed[] = $name;
            $this->was_dependence($name);
        }
    }
}
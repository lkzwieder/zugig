<?php
class SFTP {
    private $con;
    private $stream;
    //private $sftp;

    public function __construct($sftp_name, Array $params = []) {
        $settings = parse_ini_file(SFTP, true);
        try {
            if(!$this->con = ssh2_connect($settings[$sftp_name]['host'], $settings[$sftp_name]['port'], $params))
                throw new Exception("Cannot connect to the sftp server");
            if(!ssh2_auth_password($this->con, $settings[$sftp_name]['username'], $settings[$sftp_name]['password']))
                throw new Exception("Bad username or password");
            //$this->sftp = ssh2_sftp($this->con);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function list_current_dir($print = true) {
        $this->stream = $this->exec("ls -al");
        return $print ? $this->get_response() : (bool) $this->stream;
    }

    public function cd($path, $print = false) {
        $this->stream = $this->exec("cd ".$path);
        return $print ? $this->get_response() : (bool) $this->stream;
    }

    public function upload_file() {

    }

    public function download_file() {

    }

    public function close() {
        return fclose($this->stream);
    }

    private function exec($cmd, $block = true) {
        $this->stream = ssh2_exec($this->con, $cmd);
        stream_set_blocking($this->stream, $block);
        return (bool) $this->stream;
    }

    private function get_response() {
        $data = "";
        while($buf = fread($this->stream, 4096)) {
            $data .= $buf;
        }
        return $data;
    }
}
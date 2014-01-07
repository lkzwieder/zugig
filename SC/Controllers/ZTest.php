<?php
class Controllers_ZTest extends TestController {
    public function test_dependencies() {
        $d = new Dependencies();
        $d->add_data("segundo", "segundo", ["primero"]);
        $d->add_data("quinto", "quinto", ["primero", "cuarto"]);
        $d->add_data("primero", "primero");
        $d->add_data("cuarto", "cuarto", ["tercero"]);
        $d->add_data("tercero", "tercero", ["segundo"]);
        echo $this->assert($d->get_data(), ["primero", "segundo", "tercero", "cuarto", "quinto"], "Salio todo joya");
    }

    public function test_glue_js() {
        $this->set_vars([
            'title' => 'Test Glue JS',
            'glue_js' => GlueJS::get_instance(),
        ]);
        $this->render("gluejs");
    }

    public function test_glue_pack() {
        $g = new Glue();
        $g->begin_tag_data();?>
        <script>(function(){sc.initModule($('#segundo'));})();</script>
        <?php $g->end_tag_data("segundo", ["primero"]);
        $g->begin_tag_data();?>
        <script>(function(){sc.initModule($('#tercero'));})();</script>
        <?php $g->end_tag_data("tercero", ["segundo"]);
        $g->begin_tag_data();?>
        <script>(function(){sc.initModule($('#primero'));})();</script>
        <?php $g->end_tag_data("primero");
        echo $this->assert($g->get_packed_data(), "(function(){sc.initModule($('#primero'));})();(function(){sc.initModule($('#segundo'));})();(function(){sc.initModule($('#tercero'));})();");
    }

    public function test_glue_urls() {
        $g = new Glue();
        $g->set_url_data("/public/js/sc.js", "sc", ["handler"]);
        $g->set_url_data("/public/js/lib/jquery-2.0.3.min.js", "jquery");
        $g->set_url_data("/public/js/lib/jquery.uriHandler-0.1.js", "handler", ["jquery"]);
        $this->pre_var_dump($g->get_packed_data());
    }

    public function dummy_test() {
        echo file_get_contents(APP_ROOT."/public/js/lib/jquery.uriHandler-0.1.js");
    }

    public function test_gluejs() {
        $g = GlueJS::get_instance();
        $g->set_url_data("/public/js/sc.js", "sc", ["handler"]);
        $g->begin_tag_data();?>
        <script>(function(){sc.initModule($('#primero'));})();</script>
        <?php $g->end_tag_data("primero", ["sc"]);
        $g->set_url_data("/public/js/lib/jquery-2.0.3.min.js", "jquery");
        $g->set_url_data("/public/js/lib/jquery.uriHandler-0.1.js", "handler", ["jquery"]);
        $g->set_url_data("/public/js/sc.shell.js", "shell", ["sc"]);
        $this->pre_var_dump($g->flush());
    }
}
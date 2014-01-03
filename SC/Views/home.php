<!DOCTYPE html>
<html>
<head>
    <title><?=$view->title?></title>
    <?php
    $view->glue_js->set_url_data("/public/js/lib/jquery-2.0.3.min.js", "jquery");
    $view->glue_js->set_url_data("/public/js/lib/jquery.uriHandler-0.1.js", "uri_handler", ["jquery"]);
    $view->glue_js->set_url_data("/public/js/sc.js", "sc", ["uri_handler"]);
    $view->glue_js->set_url_data("/public/js/sc.test.js", "test", ["shell"]);
    $view->glue_js->set_url_data("/public/js/sc.shell.js", "shell", ["sc"]);
    ?>
</head>
<body>
    <div id="sc"></div>
</body>
<?php $view->glue_js->begin_tag_data()?>
<script>(function(){sc.initModule($('#sc'));})();</script>
<?php $view->glue_js->end_tag_data("test")?>
<?php $view->glue_js->print_tag()?>
</html>
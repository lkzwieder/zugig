<?php
# zugig loader
define('APP_ROOT', __DIR__);
function path() {return implode(DIRECTORY_SEPARATOR, $parts = func_get_args());}
$config_path = DIRECTORY_SEPARATOR.path('home', 'sp3ctr4l', 'zugig').DIRECTORY_SEPARATOR;
$main_settings = parse_ini_file($config_path.'zugig.ini');
define("ENVIROMENT", $main_settings['enviroment']);
define("DEFAULT_CONTROLLER", $main_settings['default_controller']);
define("DEFAULT_ACTION", $main_settings['default_action']);
require_once $main_settings['loader'];

# xhprof init
try {
    $xhprof = isset($_GET['profiling']) && $_GET['profiling'] == 1 && extension_loaded('xhprof');
    if($xhprof) {
        $xhprof_path = path(APP_ROOT, 'Lib', 'xhprof', 'xhprof_lib', 'utils');
        require_once $xhprof_path.DIRECTORY_SEPARATOR.'xhprof_lib.php';
        require_once $xhprof_path.DIRECTORY_SEPARATOR.'xhprof_runs.php';
        xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    }

    # routes settings - entry points
    $router = Router::get_instance();
    $router->set_route('/'); # , ['controller' => 'home', 'action' => 'index']
    $router->set_route('/news/:id/:another/:caca', ['controller' => 'home', 'action' => 'home'], ['id' => '[\d]{1,8}', 'another' => '[a-z]{3}']);
    $router->run();

    # xhprof collect and save info
    if($xhprof) {
        $profiler_namespace = 'Lkz';
        $xhprof_data = xhprof_disable();
        $xhprof_runs = new XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace);

        # url to the XHProf UI libraries
        $profiler_url = sprintf('/Lib/xhprof/xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);?>
        <a href="<?=$profiler_url?>" target="_blank">Profiler output</a>
    <?php }
} catch(Exception $e) {
    echo $e->getMessage();
}
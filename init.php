<?php
define("ROOT", __DIR__);
define("DATABASES", $config_path."databases.ini");
define("SFTP", $config_path."sftp.ini");

# $main_settings must be declared in the index file of the application
define("CSS_MINIFIER", $main_settings['css']['minify']);
define("JS_MINIFIER", $main_settings['js']['minify']);
define("ENVIROMENT", $main_settings['base']['enviroment']);
define("DEFAULT_CONTROLLER", $main_settings['base']['default_controller']);
define("DEFAULT_ACTION", $main_settings['base']['default_action']);

# TODO implement native autoload with namespaces capability
require_once path(ROOT, 'Zugig', 'Classes', 'Autoload.php');
$autoloader = Autoload::get_instance();
$autoloader->set_path(path(ROOT, 'Zugig', 'Classes'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Interfaces'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Traits'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Lib'));
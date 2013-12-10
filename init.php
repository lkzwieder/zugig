<?php
define("ROOT", __DIR__);
define("DATABASES", $config_path."databases.ini");
define("SFTP", $config_path."sftp.ini");
require_once path(ROOT, 'Zugig', 'Classes', 'Autoload.php');
$autoloader = Autoload::get_instance();
$autoloader->set_path(path(ROOT, 'Zugig', 'Classes'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Interfaces'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Traits'));
$autoloader->set_path(path(ROOT, 'Zugig', 'Lib'));
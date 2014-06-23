<?php


// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main-free.php';

define('ORE_VERSION_NAME', 'Open Real Estate FREE');
define('ORE_VERSION', '1.8.1');

define('ROOT_PATH', dirname(__FILE__));

define('IS_FREE', true);

//remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
 
define('ALREADY_INSTALL_FILE', ROOT_PATH . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR
                                . 'runtime' . DIRECTORY_SEPARATOR . 'already_install');

require_once($yii);
Yii::createWebApplication($config)->run();

<?php

// change the following paths if necessary
$yii    = dirname(__FILE__).'/../../yii/framework/yii.php';
$config = dirname(__FILE__).'/_application/config/main.php';
$app    = dirname(__FILE__).'/_application/KXMApplication.php';


// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once($app);

Yii::createApplication('KXMApplication', $config)->run();
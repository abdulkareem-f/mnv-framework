<?php

use MNV\Core\App;

$baseDirPath = __DIR__ . '/..';

require_once $baseDirPath . '/vendor/autoload.php';
require_once $baseDirPath . '/routes/web.php';


$app = new App($baseDirPath);
$app->run();

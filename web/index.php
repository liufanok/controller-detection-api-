<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../config/bootstrap.php';

$config = require __DIR__ . '/../config/main.php';

try {

    (new yii\web\Application($config))->run();

} catch (\app\models\ApiException $e) {
    responseError($e);
} catch (HttpException $e) {
    responseError($e);
} catch (\Exception $e) {
    responseError($e);
}


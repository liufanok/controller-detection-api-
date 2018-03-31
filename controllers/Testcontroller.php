<?php
/**
 * Created by PhpStorm.
 * User: liufan
 * Date: 2018/3/31
 * Time: 下午4:54
 */

namespace app\controllers;

use yii\web\Controller;

class TestController extends Controller
{
    public function actionHello()
    {
        $a = ['a' => '.'];
        echo json_encode($a);
    }
}
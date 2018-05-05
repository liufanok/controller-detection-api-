<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use yii\web\Controller;

class BaseController extends Controller
{
    public $layout = false;
    private static $notNeedLogin = ['login'];
    public $username;
    public $role;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws ApiException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $strActionId = strtolower($action->id);

        if (parent::beforeAction($action)) {//检验用户是否登录
            if (!in_array($strActionId, self::$notNeedLogin) && \Yii::$app->user->isGuest) {
                throw new ApiException(ApiCodeDesc::USER_NOT_LOGIN);
            }
            $this->username = \Yii::$app->user->identity->username;
            return true;
        }
        return false;
    }

    /**
     * 获取post传过来的参数
     * @param $paramName
     * @param string $default
     * @return string
     */
    public function safeGetParam($paramName, $default = '')
    {

        $request = $_POST;//为了安全只接受post参数
        return isset($request["$paramName"]) ? trim($request["$paramName"]) : $default;
    }
}
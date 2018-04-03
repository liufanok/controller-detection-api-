<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\User;

class SiteController extends BaseController
{
    /**
     * 登录失败
     * @throws ApiException
     */
    public function actionLogin()
    {
        $username = $this->safeGetParam("username");
        $pwd = $this->safeGetParam("password");

        if (User::login($username, $pwd)) {
            responseOK();
        } else {//登录失败
            throw new ApiException(ApiCodeDesc::LOGIN_FAILED);
        }
    }

    /**
     * 退出
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        responseOK();
    }

    /**
     * 用户未登录
     * @throws ApiException
     */
    public function actionAdminInfo()
    {
        $username = \Yii::$app->user->identity->username;
        if ($username) {
            responseOK(['name' => $username]);
        } else {
            throw new ApiException(ApiCodeDesc::USER_NOT_LOGIN);
        }
    }
}
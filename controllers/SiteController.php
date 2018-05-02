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
        if (empty($username) || empty($pwd)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (User::login($username, $pwd)) {
            $user = \Yii::$app->user->identity;
            responseOK($user->getAuthKey());
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
    public function actionUserInfo()
    {
        $user = \Yii::$app->user->identity;
        $role = $user->roles;
        if ($user) {
            $data = [
                'roles' => [$role],
                'token' => 'gTW4LlbxSMCwQ4KIBrIOXV3C3i2OiLBZ',
                'introduction' => '',
                'avatar' => '',
                'name' => $user->username,
            ];
            responseOK($data);
        } else {
            throw new ApiException(ApiCodeDesc::USER_NOT_LOGIN);
        }
    }
}
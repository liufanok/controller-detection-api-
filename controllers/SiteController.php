<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\User;

class SiteController extends BaseController
{
    /**
     * 登录
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
     * 用户信息
     * @throws ApiException
     */
    public function actionUserInfo()
    {
        $user = \Yii::$app->user->identity;
        $role = $user->roles;
        $last = $user->last_login_time ? date("Y-m-d H:i", strtotime($user->last_login_time)) : '无';
        if ($user) {
            $data = [
                'roles' => [$role],
                'token' => 'gTW4LlbxSMCwQ4KIBrIOXV3C3i2OiLBZ',
                'introduction' => '',
                'avatar' => '',
                'name' => $user->username,
                'phone' => $user->phone,
                'email' => $user->email,
                'last_login_time' => $last,
            ];
            responseOK($data);
        } else {
            throw new ApiException(ApiCodeDesc::USER_NOT_LOGIN);
        }
    }

    /**
     * 请求重置密码
     * @throws ApiException
     * @throws \yii\base\Exception
     */
    public function actionResetPasswordRequest()
    {
        $username = $this->safeGetParam("username");
        User::resetPasswordRequest($username);
        responseOK();
    }

    /**
     * 修改密码
     * @throws \yii\base\Exception
     */
    public function actionResetPassword()
    {
        $token = $this->safeGetParam("token");
        $password = $this->safeGetParam("password");
        $user = User::findByPasswordResetToken($token);
        if (empty($user)) {
            throw new ApiException(ApiCodeDesc::ERR_REQUEST_FORBIDDEN);
        }
        if (!User::checkPassword($password)) {//密码强度检验
            throw new ApiException(ApiCodeDesc::PASSWORD_TOO_WEAK);
        }
        $user->password_hash = \Yii::$app->security->generatePasswordHash($password);
        $user->password_reset_token = null;
        $user->save();
        responseOK();
    }
}
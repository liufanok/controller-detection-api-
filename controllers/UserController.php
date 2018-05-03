<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\User;

class UserController extends BaseController
{
    /**
     * 用户列表
     * @throws ApiException
     */
    public function actionUserList()
    {
        $username = $this->safeGetParam("username", null);
        $phone = $this->safeGetParam("phone", null);
        $email = $this->safeGetParam("email", null);
        $status = $this->safeGetParam("status", null);
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);

        //参数检验
        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $list = User::search($username, $email, $phone, $status, $page, $limit);
        responseOK($list);
    }

}
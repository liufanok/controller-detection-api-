<?php

namespace app\models;

class ApiCodeDesc
{
    //成功码
    const SUCCESS = 0;//成功

    //错误码
    const USER_NOT_LOGIN = 100;//用户未登录
    const ERR_YII_CODE_ERROR = 9999;//PHP框架报的错误码

    public static $arrApiErrDesc = [
        self::SUCCESS => 'success',
        self::USER_NOT_LOGIN => '用户未登录',
        self::ERR_YII_CODE_ERROR => '系统错误',
    ];
}
<?php

namespace app\models;

class ApiCodeDesc
{
    //成功码
    const SUCCESS = 0;//成功

    //1~99系统相关错误
    const ERR_PARAM_INVALID = 2;//参数错误

    //错误码100~200登录相关错误
    const USER_NOT_LOGIN = 100;//用户未登录
    const USER_NOT_EXISTS_OR_FORBIDDEN = 101;//用户不存在或被禁用
    const PASSWORD_INVALID = 102;//密码错误
    const SET_PASSWORD_FIRST = 103;//先设置密码
    const LOGIN_FAILED = 104;//登录失败
    const USERNAME_EXISTS = 105;//用户名已存在
    const PHONE_EXISTS = 106;//手机号已存在
    const EMAIL_EXISTS = 107;//邮箱已存在
    const USER_NOT_EXISTS = 108;//用户不存在

    const ERR_YII_CODE_ERROR = 9999;//PHP框架报的错误码

    //错误码和错误信息对应关系
    public static $arrApiErrDesc = [
        self::SUCCESS => 'success',

        self::ERR_PARAM_INVALID => '请求参数错误',

        self::USER_NOT_LOGIN => '用户未登录',
        self::USER_NOT_EXISTS_OR_FORBIDDEN => '用户不存在或被禁用',
        self::PASSWORD_INVALID => '密码错误',
        self::SET_PASSWORD_FIRST => '请先设置密码',
        self::LOGIN_FAILED => '登录失败',
        self::USERNAME_EXISTS => '用户名已存在',
        self::PHONE_EXISTS => '手机号已存在',
        self::EMAIL_EXISTS => '邮箱已存在',
        self::USER_NOT_EXISTS => '用户不存在',

        self::ERR_YII_CODE_ERROR => '系统错误',
    ];
}
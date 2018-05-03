<?php

namespace app\models;

class ApiCodeDesc
{
    //成功码
    const SUCCESS = 0;//成功

    //1~99系统相关错误
    const ERR_PARAM_INVALID = 2;//参数错误
    const ERR_DB_INSERT_DATA_ERROR = 3; //插入数据库失败
    const ERR_DB_UPDATE_DATA_ERROR = 4; //更新数据库失败

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

    const SAME_PLANT = 200;//有一个重名的厂区
    const PLANT_HAS_WORKSHOP = 201;//厂区有车间，不支持删除
    const SAME_WORKSHOP = 303;//有一个同名的车间
    const WORKSHOP_HAS_LOOP = 304;//车间有回路
    const SAME_LOOPS = 305;//有同名的回路
    const LOOP_HAS_DATA = 306;//回路有数据

    const ERR_YII_CODE_ERROR = 9999;//PHP框架报的错误码

    //错误码和错误信息对应关系
    public static $arrApiErrDesc = [
        self::SUCCESS => 'success',

        self::ERR_PARAM_INVALID => '请求参数错误',
        self::ERR_DB_INSERT_DATA_ERROR=>'数据库插入失败',
        self::ERR_DB_UPDATE_DATA_ERROR=>'数据库更新失败',

        self::USER_NOT_LOGIN => '用户未登录',
        self::USER_NOT_EXISTS_OR_FORBIDDEN => '用户不存在或被禁用',
        self::PASSWORD_INVALID => '密码错误',
        self::SET_PASSWORD_FIRST => '请先设置密码',
        self::LOGIN_FAILED => '登录失败',
        self::USERNAME_EXISTS => '用户名已存在',
        self::PHONE_EXISTS => '手机号已存在',
        self::EMAIL_EXISTS => '邮箱已存在',
        self::USER_NOT_EXISTS => '用户不存在',

        self::SAME_PLANT=>'该厂区名已存在',
        self::PLANT_HAS_WORKSHOP=>'该厂区下有车间，暂不支持删除',
        self::SAME_WORKSHOP=>'该车间名已存在',
        self::WORKSHOP_HAS_LOOP=>'该车间有回路，暂不支持删除',
        self::SAME_LOOPS=>'该回路名已存在',
        self::LOOP_HAS_DATA=>'该回路有数据，暂不支持删除',

        self::ERR_YII_CODE_ERROR => '系统错误',
    ];
}
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
    const ERR_REQUEST_FORBIDDEN = 5;//禁止请求
    const ERR_LOAD_EXCEL_FAIL = 6;//excel文件上传失败
    const ERR_EXCEL_IS_NULL = 7;//excel数据为空
    const ERR_EXCEL_GET_MAX = 8;//excel文件达到最大的数量
    const ERR_HAS_NO_ACCESS = 9;//无权限

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
    const PASSWORD_TOO_WEAK = 109;//密码强度太弱
    const USERNAME_NOT_NULL = 110;//用户名不能为空
    const PHONE_NOT_NULL = 111;//手机号不能为空
    const EMAIL_NOT_NULL = 112;//email不能为空
    const PHONE_ILLEGAL = 113;//手机号非法
    const EMAIL_ILLEGAL = 114;//邮箱非法

    const SAME_PLANT = 200;//有一个重名的厂区
    const PLANT_HAS_WORKSHOP = 201;//厂区有车间，不支持删除
    const SAME_WORKSHOP = 303;//有一个同名的车间
    const WORKSHOP_HAS_LOOP = 304;//车间有回路
    const SAME_LOOPS = 305;//有同名的回路
    const LOOP_HAS_DATA = 306;//回路有数据
    const NO_DATA = 307;//

    const ERR_YII_CODE_ERROR = 9999;//PHP框架报的错误码

    //错误码和错误信息对应关系
    public static $arrApiErrDesc = [
        self::SUCCESS => 'success',

        self::ERR_PARAM_INVALID => '请求参数错误',
        self::ERR_DB_INSERT_DATA_ERROR =>'数据库插入失败',
        self::ERR_DB_UPDATE_DATA_ERROR =>'数据库更新失败',
        self::ERR_REQUEST_FORBIDDEN => 'Request Forbidden!',
        self::ERR_LOAD_EXCEL_FAIL => 'excel文件上传失败',
        self::ERR_EXCEL_IS_NULL => 'excel文件数据为空',
        self::ERR_EXCEL_GET_MAX => '最大不超过1000条数据',
        self::ERR_HAS_NO_ACCESS => '您暂无权限',

        self::USER_NOT_LOGIN => '用户未登录',
        self::USER_NOT_EXISTS_OR_FORBIDDEN => '用户不存在或被禁用',
        self::PASSWORD_INVALID => '密码错误',
        self::SET_PASSWORD_FIRST => '请先设置密码',
        self::LOGIN_FAILED => '登录失败',
        self::USERNAME_EXISTS => '用户名已存在',
        self::PHONE_EXISTS => '手机号已存在',
        self::EMAIL_EXISTS => '邮箱已存在',
        self::USER_NOT_EXISTS => '用户不存在',
        self::PASSWORD_TOO_WEAK => '密码强度太弱',
        self::USERNAME_NOT_NULL => '用户名不能为空',
        self::PHONE_NOT_NULL => '手机号不能为空',
        self::EMAIL_NOT_NULL => '邮箱不能为空',
        self::PHONE_ILLEGAL => '手机号非法',
        self::EMAIL_ILLEGAL => '邮箱非法',

        self::SAME_PLANT=>'该厂区名已存在',
        self::PLANT_HAS_WORKSHOP=>'该厂区下有车间，暂不支持删除',
        self::SAME_WORKSHOP=>'该车间名已存在',
        self::WORKSHOP_HAS_LOOP=>'该车间有回路，暂不支持删除',
        self::SAME_LOOPS=>'该回路名已存在',
        self::LOOP_HAS_DATA=>'该回路有数据，暂不支持删除',

        self::ERR_YII_CODE_ERROR => '系统错误',
    ];
}
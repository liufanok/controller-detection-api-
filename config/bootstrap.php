<?php

/**
 * API返回格式
 * @param $code
 * @param string $message
 * @param null $data
 */
function returnData($code, $message = 'success', $data = null)
{

    $returnData = [
        'code' => $code,
        'message' => $message,
    ];

    if (null !== $data) {
        $returnData['data'] = $data;
    }

    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    \Yii::$app->response->content = json_encode($returnData);

    try {
        \Yii::$app->end();
    } catch (\yii\base\ExitException $e) {
        exit;
    }

}

/**
 * 返回正常的数据
 * @param null $data
 */
function responseOK($data = null)
{
    returnData(app\models\ApiCodeDesc::SUCCESS, 'success', $data);
}

/**
 * 输出Exception信息,返回错误信息中,error_code肯定不是0
 * @param Exception $e
 */
function responseError(Exception $e)
{
    $errCode = $e->getCode();
    !$errCode && $errCode = app\models\ApiCodeDesc::ERR_YII_CODE_ERROR;
    \Yii::error($e->getTraceAsString());

    returnData($errCode, $e->getMessage());
}

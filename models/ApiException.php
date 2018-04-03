<?php

namespace app\models;

use yii\web\HttpException;

class ApiException extends HttpException
{
    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($code = 0, $message = null, $status = 200, \Exception $previous = null)
    {
        if (null === $message) {
            $message = isset(ApiCodeDesc::$arrApiErrDesc[$code]) ? ApiCodeDesc::$arrApiErrDesc[$code]: '';
        }
        parent::__construct($status, $message, $code, $previous);
    }
}
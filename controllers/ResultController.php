<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\Loops;
use app\models\Result;

class ResultController extends BaseController
{
    /**
     * 获取时间范围列表
     * @throws ApiException
     */
    public function actionTimeList()
    {
        $loopId = $this->safeGetParam("loop_id");
        $date = $this->safeGetParam("date");

        if (!Loops::userHasAccess(\Yii::$app->user->identity, $loopId)) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }
        if (empty($loopId) || empty($date)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        $data = Result::getTimeListByLoopIdAndDate($loopId, $date);
        responseOK($data);
    }

    /**
     * 报告的数据
     * @throws ApiException
     */
    public function actionReport()
    {
        $date = $this->safeGetParam("date");
        $scope = $this->safeGetParam("scope");
        $loopId = $this->safeGetParam("loop_id");

        $timeArr = explode('-', $scope);
        if (empty($timeArr)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $result = Result::findOne(['loop_id' => $loopId, 'date' => $date, 'start_time' => $timeArr[0], 'end_time' => $timeArr[1]]);
        if (empty($result)) {
            responseOK([]);
        }
        if (!Loops::userHasAccess(\Yii::$app->user->identity, $result->loop_id)) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $data = Result::reportDate($result);
        responseOK($data);
    }
}
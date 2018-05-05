<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
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
        $resultId = $this->safeGetParam("result_id");
        $result = Result::findOne($resultId);
        if (!$result) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $data = Result::reportDate($result);
        responseOK($data);
    }
}
<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\Data;
use app\models\Loops;
use app\models\Result;
use app\models\Workshop;

class LoopsController extends BaseController
{
    /**
     * 添加回路
     * @throws ApiException
     */
    public function actionAddLoop()
    {
        $workshopId = $this->safeGetParam("workshop_id");
        $name = $this->safeGetParam("name");
        if (!Workshop::findOne($workshopId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Loops::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_LOOPS);
        }
        $res = Loops::add($workshopId, $name);
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_INSERT_DATA_ERROR);
        }
    }

    /**
     * 删除某个回路
     * @throws ApiException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionsDeleteLoop()
    {
        $id = $this->safeGetParam("id");
        $loop = Loops::findOne($id);
        if (!$loop) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Result::findOne(['loop_id' => $id]) || Data::findOne(['loop_id' => $id])) {
            throw new ApiException(ApiCodeDesc::LOOP_HAS_DATA);
        }
        $res = $loop->delete();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }

    /**
     * 修改回路信息
     * @throws ApiException
     */
    public function actionUpdateLoop()
    {
        $id = $this->safeGetParam("id");
        $workshopId = $this->safeGetParam("workshop_id");
        $name = $this->safeGetParam("name");

        $loop = Loops::findOne($id);
        if (empty($loop) || empty($name)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (!Workshop::findOne($workshopId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Loops::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_LOOPS);
        }

        $loop->workshop_id = $workshopId;
        $loop->name = $name;
        $res = $loop->save();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }
}
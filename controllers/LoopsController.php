<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\Data;
use app\models\Loops;
use app\models\Result;
use app\models\User;
use app\models\Workshop;

class LoopsController extends BaseController
{
    /**
     * 回路列表
     * @throws ApiException
     */
    public function actionLoopList()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $name = $this->safeGetParam("name");
        $workshopId = $this->safeGetParam("workshop_id", '');
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);

        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if ($workshopId && !Workshop::findOne($workshopId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $data = Loops::search($name, $workshopId, $page, $limit);
        responseOK($data);
    }

    /**
     * 添加回路
     * @throws ApiException
     */
    public function actionAddLoop()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

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
    public function actionDeleteLoop()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

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
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

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
        if ($name != $loop->name && Loops::findOne(['name' => $name])) {
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

    /**
     * 获取用户能看到的回路数据
     */
    public function actionUserLoops()
    {
        $user = \Yii::$app->user->identity;
        $name = $this->safeGetParam("name");
        $workshopId = $this->safeGetParam("workshop_id");
        $page = $this->safeGetParam("page");
        $limit = $this->safeGetParam("limit");

        $data = Loops::getLoopsByUser($user, $name, $workshopId, $page, $limit);
        responseOK($data);
    }
}
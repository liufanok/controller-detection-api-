<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\Loops;
use app\models\Plant;
use app\models\User;
use app\models\Workshop;

class WorkshopController extends BaseController
{
    /**
     * 获取搜索车间列表
     * @throws ApiException
     */
    public function actionWorkshopList()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $name = $this->safeGetParam("name");
        $plantId = $this->safeGetParam("plant_id", '');
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);

        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if ($plantId && !Plant::findOne($plantId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $data = Workshop::search($name, $plantId, $page, $limit);
        responseOK($data);
    }

    /**
     * 添加车间
     * @throws ApiException
     */
    public function actionAddWorkshop()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $name = $this->safeGetParam("name");
        $plantId = $this->safeGetParam("plant_id");

        if (!Plant::findOne($plantId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Workshop::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_WORKSHOP);
        }

        $res = Workshop::add($name, $plantId);
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_INSERT_DATA_ERROR);
        }
    }

    /**
     * 删除车间
     * @throws ApiException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteWorkshop()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $id = $this->safeGetParam("id");
        $workshop = Workshop::findOne($id);
        if (!$workshop) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Loops::findOne(['workshop_id' => $id])) {
            throw new ApiException(ApiCodeDesc::WORKSHOP_HAS_LOOP);
        }

        $res = $workshop->delete();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }

    /**
     * 修改车间信息
     * @throws ApiException
     */
    public function actionUpdateWorkshop()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $id = $this->safeGetParam("id");
        $name = $this->safeGetParam("name");
        $plantId = $this->safeGetParam("plant_id");

        $workshop = Workshop::findOne($id);
        if (!$workshop || empty($name)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if ($name != $workshop->name && Workshop::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_WORKSHOP);
        }
        if (!Plant::findOne($plantId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $workshop->name = $name;
        $workshop->plant_id = $plantId;
        $res = $workshop->save();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }

    /**
     * 根据厂区获取车间信息
     * @throws ApiException
     */
    public function actionGetWorkshopByPlant()
    {
        $plantId = $this->safeGetParam("plant_id");
        if(!Plant::findOne($plantId)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        $data = Workshop::getByPlantId($plantId);
        responseOK($data);
    }
}
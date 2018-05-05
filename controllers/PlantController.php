<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\Plant;
use app\models\Workshop;

class PlantController extends BaseController
{
    /**
     * 获取厂区列表/搜索厂区
     * @throws ApiException
     */
    public function actionPlantList()
    {
        $name = $this->safeGetParam("name");
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);

        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $data = Plant::search($name, $page, $limit);
        responseOK($data);
    }

    /**
     * 增加新的厂区
     * @throws ApiException
     */
    public function actionAddPlant()
    {
        $name = $this->safeGetParam("name");

        if (empty($name)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Plant::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_PLANT);
        }

        $res = Plant::add($name);
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_INSERT_DATA_ERROR);
        }
    }

    /**
     * 山吃某个厂区
     * @throws ApiException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeletePlant()
    {
        $id = $this->safeGetParam("id");
        $plant = Plant::findOne($id);
        if (!$plant) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if (Workshop::findOne(['plant_id' => $id])) {
            throw new ApiException(ApiCodeDesc::PLANT_HAS_WORKSHOP);
        }

        $res = $plant->delete();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }

    /**
     * 修改厂区的名称
     * @throws ApiException
     */
    public function actionUpdatePlant()
    {
        $id = $this->safeGetParam("id");
        $name = $this->safeGetParam("name");
        $plant = Plant::findOne($id);
        if (!$plant || empty($name)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        if ($name != $plant->name && Plant::findOne(['name' => $name])) {
            throw new ApiException(ApiCodeDesc::SAME_PLANT);
        }

        $plant->name = $name;
        $res = $plant->save();
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }

    /**
     * 获取所有的厂区
     */
    public function actionGetAllPlant()
    {
        $data = Plant::getAll();
        responseOK($data);
    }
}
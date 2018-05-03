<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\User;

class UserController extends BaseController
{
    /**
     * 用户列表
     * @throws ApiException
     */
    public function actionUserList()
    {
        $username = $this->safeGetParam("username", null);
        $phone = $this->safeGetParam("phone", null);
        $email = $this->safeGetParam("email", null);
        $status = $this->safeGetParam("status", null);
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);

        //参数检验
        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $list = User::search($username, $email, $phone, $status, $page, $limit);
        responseOK($list);
    }

    /**
     * 添加用户
     * @throws ApiException
     * @throws \yii\base\Exception
     */
    public function actionAddUser()
    {
        $username = $this->safeGetParam("username");
        $phone = $this->safeGetParam("phone");
        $email = $this->safeGetParam("email");
        User::addUser($username, $phone, $email);
        responseOK();
    }

    /**
     * 修改用户信息
     * @throws ApiException
     */
    public function actionUpdateUser()
    {
        $id = $this->safeGetParam("id");
        $phone = $this->safeGetParam("phone");
        $email = $this->safeGetParam("email");
        $status = $this->safeGetParam("status");

        if (!in_array($status, [User::STATUS_NORMAL, User::STATUS_DELETED])) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        User::updateUser($id, $phone, $email, $status);
        responseOK();
    }

    /**
     * 批量导入用户
     * @throws ApiException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionBatchImportUser()
    {
        $filePath = $_FILES['file']['tmp_name'];
        $phpExcelReader = new \PHPExcel_Reader_Excel2007();
        if (!$phpExcelReader->canRead($filePath)) {
            $phpExcelReader = new \PHPExcel_Reader_Excel5();
            if (!$phpExcelReader->canRead($filePath)) {
                throw new ApiException(ApiCodeDesc::ERR_LOAD_EXCEL_FAIL);
            }
        }
        $excel = $phpExcelReader->load($filePath);
        //读取excel文件中的第一个工作表
        $currentSheet = $excel->getSheet(0);
        //取得最大的列号
        $allColumn = $currentSheet->getHighestColumn();
        //取得一共有多少行
        $allRow = $currentSheet->getHighestRow();
        if ($allRow == 1) {
            throw new ApiException(ApiCodeDesc::ERR_EXCEL_IS_NULL);
        }
        if ($allRow >= 1001) {
            throw new ApiException(ApiCodeDesc::ERR_EXCEL_GET_MAX);
        }

        $success = 0;
        $failed = [];
        for ($currentRow = 2; $currentRow <= $allRow; ++$currentRow) {//读取数据并导入用户
            $username = strval($currentSheet->getCell('A' . $currentRow)->getValue());
            $phone = strval($currentSheet->getCell('B' . $currentRow)->getValue());
            $email = strval($currentSheet->getCell('C' . $currentRow)->getValue());
            try {
                $res = User::addUser($username, $phone, $email);
                if (!$res) {
                    throw new ApiException(ApiCodeDesc::ERR_DB_INSERT_DATA_ERROR);
                }
            } catch (ApiException $e) {
                $message = $e->getMessage();
                $failed[] = "第{$currentRow}行：$message";
                continue;
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $failed[] = "第{$currentRow}行：$message";
                continue;
            }
            $success ++;
        }

        $data = [
            'total' => $allRow - 1,
            'success_num' => $success,
            'failed' => [
                'num' => count($failed),
                'failed_info' => $failed,
            ],
        ];
        responseOK($data);
    }
}
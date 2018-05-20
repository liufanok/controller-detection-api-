<?php

namespace app\controllers;

use app\models\ApiCodeDesc;
use app\models\ApiException;
use app\models\ExcelHelper;
use app\models\User;
use app\models\UserBelong;

class UserController extends BaseController
{
    /**
     * 用户列表
     * @throws ApiException
     */
    public function actionUserList()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $username = $this->safeGetParam("username", null);
        $phone = $this->safeGetParam("phone", null);
        $email = $this->safeGetParam("email", null);
        $status = $this->safeGetParam("status", null);
        $page = $this->safeGetParam("page", 1);
        $limit = $this->safeGetParam("limit", 10);
        $role = $this->safeGetParam("role");

        //参数检验
        if (!is_numeric($page) || !is_numeric($limit)) {//参数检验
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        $list = User::search($username, $email, $phone, $status, $page, $limit, $role);
        responseOK($list);
    }

    /**
     * 添加用户
     * @throws ApiException
     * @throws \yii\base\Exception
     */
    public function actionAddUser()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $username = $this->safeGetParam("username");
        $phone = $this->safeGetParam("phone");
        $email = $this->safeGetParam("email");
        $role = $this->safeGetParam("role");
        User::addUser($username, $phone, $email, $role);
        responseOK();
    }

    /**
     * 修改用户信息
     * @throws ApiException
     */
    public function actionUpdateUser()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        $id = $this->safeGetParam("id");
        $phone = $this->safeGetParam("phone");
        $email = $this->safeGetParam("email");
        $status = $this->safeGetParam("status");
        $role = $this->safeGetParam("role");

        if (!in_array($status, [User::STATUS_NORMAL, User::STATUS_DELETED])) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }

        User::updateUser($id, $phone, $email, $status, $role);
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
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }

        require_once('../vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
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
                $res = User::addUser($username, $phone, $email, 'normal');
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

    /**
     * 批量导入用户模板
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public static function actionTemplate()
    {
        ExcelHelper::exportExcel('批量导入用户', '批量导入', ['用户名', '手机号', '邮箱'], []);
        responseOK();
    }

    /**
     * 分配厂区/车间
     * @throws ApiException
     * @throws \yii\db\Exception
     */
    public function actionDistribution()
    {
        if ($this->role != User::ROLE_ADMIN) {
            throw new ApiException(ApiCodeDesc::ERR_HAS_NO_ACCESS);
        }
        $userId = $this->safeGetParam("user_id");
        $distribution = $this->safeGetParam("distribution");

        $user = User::findOne($userId);
        $distributionArr = json_decode($distribution, true);
        if (empty($user) || !is_array($distributionArr)) {
            throw new ApiException(ApiCodeDesc::ERR_PARAM_INVALID);
        }
        $res = UserBelong::distribution($user, $distributionArr);
        if ($res) {
            responseOK();
        } else {
            throw new ApiException(ApiCodeDesc::ERR_DB_UPDATE_DATA_ERROR);
        }
    }
}

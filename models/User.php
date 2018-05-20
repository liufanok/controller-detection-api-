<?php

namespace app\models;

use \Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $roles
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $phone
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $login_times
 * @property string $create_time
 * @property string $update_time
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = '0';//已删除
    const STATUS_NORMAL = '10';//正常
    const RESET_PASSWORD_TOKEN_EXPIRE_TIME = 3600;//重置密码token有效期
    const ROLE_ADMIN = 'admin';//管理员用户
    const ROLE_NORMAL = 'normal';//普通用户

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 获取用户列表
     * @param $username
     * @param $email
     * @param $phone
     * @param $status
     * @param $page
     * @param $limit
     * @param $role
     * @return array
     */
    public static function search($username, $email, $phone, $status, $page, $limit, $role)
    {
        $offset = ($page - 1) * $limit;
        $query = self::find()
            ->select(['id', 'username', 'roles', 'phone', 'email', 'status', 'login_times', 'create_time'])
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['like', 'username', $username])
            ->andFilterWhere(['like', 'phone', $phone])
            ->andFilterWhere(['like', 'email', $email])
            ->andFilterWhere(['roles' => $role]);
        $count = $query -> count();

        $list = $query->offset($offset)
            ->limit($limit)
            ->orderBy(["id" => SORT_DESC])
            ->asArray()
            ->all();

        $normal = [];
        foreach ($list as &$item) {
            if ($item['roles'] == self::ROLE_NORMAL) {
                $normal[] = $item['id'];
            }
            $item['create_time'] = date("Y-m-d", strtotime($item['create_time']));
        }
        $userBelongMap = UserBelong::getChineseByUserId($normal);
        foreach ($list as &$item) {
            if ($item['roles'] == self::ROLE_NORMAL) {
                $all = array_column($userBelongMap[$item['id']], 'full_name');
                $item['belong'] = implode('，', $all);
                $item['belong_arr'] = $userBelongMap[$item['id']];
            } else {
                $item['belong'] = '/';
                $item['belong_arr'] = [];
            }
        }
        $data = [
            'data' => $list ? $list : [],
            'count' => $count,
        ];
        return $data;
    }
    /**
     * @param $username
     * @param $pwd
     * @return mixed
     * @throws ApiException
     */
    public static function login($username, $pwd)
    {
        $user = self::findByUsername($username);

        if (!$user) {//用户不存在
            throw new ApiException(ApiCodeDesc::USER_NOT_EXISTS_OR_FORBIDDEN);
        }
        if ($user->password_hash == '*') {
            throw new ApiException(ApiCodeDesc::SET_PASSWORD_FIRST);
        }
        if (!Yii::$app->security->validatePassword($pwd, $user->password_hash)) {
            throw new ApiException(ApiCodeDesc::PASSWORD_INVALID);
        }

        $res = Yii::$app->user->login($user, 3600 * 8);
        if ($res) {
            $user->login_times ++;
            date_default_timezone_set("PRC");
            $user->last_login_time = date("Y-m-d H:i:s");
            $user->save();
        }

        return $res;
    }

    /**
     * 重置密码请求
     * @param $username
     * @return bool
     * @throws ApiException
     * @throws \yii\base\Exception
     */
    public static function resetPasswordRequest($username)
    {
        $user = self::findByUsername($username);
        if (empty($user)) {
            throw new ApiException(ApiCodeDesc::USER_NOT_EXISTS_OR_FORBIDDEN);
        }

        if (self::__generatePasswordResetToken($user)) {
            $prefix = 'http://liufan.me';
            $url = $prefix . "/static/resetpassword/index.html?token={$user->password_reset_token}";
            $text = "{$user->username}：您好！\n点击下面链接重置您的密码：\n {$url}";
            $res = Yii::$app
                ->mailer
                ->compose()
                ->setFrom("buct_cpa_admin@163.com")
                ->setTo($user->email)
                ->setSubject("【CPA system】这是一封密码重置链接")
                ->setTextBody($text)
                ->send();
            return $res;
        }
        return false;
    }

    /**
     *  搜索某一个未删除用户(用户名/手机号/邮箱)
     * @param $username
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByUsername($username)
    {
        return self::find()
            ->where(['or', ['username' => $username], ['phone' => $username], ['email' => $username]])
            ->andWhere(['status' => self::STATUS_NORMAL])
            ->one();
    }

    /**
     * 根据token获取用户
     * @param $token
     * @return null|static
     */
    public static function findByPasswordResetToken($token)
    {
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        if ($timestamp + self::RESET_PASSWORD_TOKEN_EXPIRE_TIME <= time()) {
            return null;
        }
        return self::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_NORMAL,
        ]);
    }

    /**
     * 根据id获取
     * @param $id
     * @return null|static
     */
    public static function getById($id)
    {
        return self::findOne(['id' => $id, 'status' => self::STATUS_NORMAL]);
    }

    /**
     * 添加用户
     * @param $username
     * @param $phone
     * @param $email
     * @param $role
     * @return bool
     * @throws ApiException
     * @throws \yii\base\Exception
     */
    public static function addUser($username, $phone, $email, $role)
    {
        if (empty($username)) {
            throw new ApiException(ApiCodeDesc::USERNAME_NOT_NULL);
        }
        if (empty($phone)) {
            throw new ApiException(ApiCodeDesc::PHONE_NOT_NULL);
        }
        if (empty($email)) {
            throw new ApiException(ApiCodeDesc::EMAIL_NOT_NULL);
        }
        if (!preg_match("/^1[34578]{1}\d{9}$/",$phone)) {
            throw new ApiException(ApiCodeDesc::PHONE_ILLEGAL);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ApiException(ApiCodeDesc::EMAIL_ILLEGAL);
        }
        //username，phone,email不能重复
        if (self::findOne(['username' => $username])) {
            throw new ApiException(ApiCodeDesc::USERNAME_EXISTS);
        }
        if (self::findOne(['phone' => $phone])) {
            throw new ApiException(ApiCodeDesc::PHONE_EXISTS);
        }
        if (self::findOne(['email' => $email])) {
            throw new ApiException(ApiCodeDesc::EMAIL_EXISTS);
        }

        $user = new User();
        $user -> username = $username;
        $user -> phone = $phone;
        $user -> email = $email;
        $user -> roles = $role;
        $user -> password_hash = '*';
        $user -> auth_key = Yii::$app->security->generateRandomString();

        return $user->save();
    }

    /**
     * 更新
     * @param $id
     * @param $phone
     * @param $email
     * @param int $status
     * @param $role
     * @return bool
     * @throws ApiException
     */
    public static function updateUser($id, $phone, $email, $status = 10, $role)
    {
        $user = self::findOne($id);
        if (!$user) {
            throw new ApiException(ApiCodeDesc::USER_NOT_EXISTS);
        }
        //phone,email不能重复
        if (self::find()->where(['and', ['phone' => $phone], ['!=', 'id', $id]])->count() > 0) {
            throw new ApiException(ApiCodeDesc::PHONE_EXISTS);
        }
        if (self::find()->where(['and', ['email' => $email], ['!=', 'id', $id]])->count() > 0) {
            throw new ApiException(ApiCodeDesc::EMAIL_EXISTS);
        }

        $user->status = $status;
        $user->roles = $role;
        $user->phone = $phone;
        $user->email = $email;
        $user->save();

        return true;
    }

    /**
     * 检查密码复杂度（密码必须要大于8位且必须包含大小写）
     * @param $password
     * @return bool
     */
    public static function checkPassword($password)
    {
        $password = trim($password);
        if ($password == null || strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
            return false;
        }
        return true;
    }

    /**
     * 生成token
     * @param User $user
     * @return bool
     * @throws \yii\base\Exception
     */
    private static function __generatePasswordResetToken(User &$user)
    {
        $token = $user->password_reset_token;
        if (!empty($token)) { //token 没过期
            $timestamp = (int) substr($token, strrpos($token, '_') + 1);
            if ($timestamp + self::RESET_PASSWORD_TOKEN_EXPIRE_TIME >= time()) {
                return true;
            }
        }

        $user->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        return $user->save();
    }
}
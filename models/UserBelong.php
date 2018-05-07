<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_belong".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $belong_id
 * @property integer $belong_type
 * @property string $create_time
 * @property string $update_time
 */
class UserBelong extends ActiveRecord
{
    const BELONG_TYPE_WORKSHOP = 1;//用户在某个车间
    const BELONG_TYPE_PLANT = 2;//用户在某个厂区
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_belong';
    }

    /**
     * 获取用户所属的车间、厂区中文
     * @param $ids
     * @return array
     */
    public static function getChineseByUserId($ids)
    {
        $plant = self::find()
            ->select(['id', 'user_id', 'belong_id', 'belong_type'])
            ->where(['user_id' => $ids])
            ->andWhere(['belong_type' => self::BELONG_TYPE_WORKSHOP])
            ->asArray()
            ->all();
        $plantIds = array_column($plant, 'belong_id');
        $plantInfos = Plant::getNameById($plantIds);

        $workshop = self::find()
            ->select(['id', 'user_id', 'belong_id', 'belong_type'])
            ->where(['user_id' => $ids])
            ->andWhere(['belong_type' => self::BELONG_TYPE_WORKSHOP])
            ->asArray()
            ->all();
        $workshopIds = array_column($workshop, 'belong_id');
        $workshopInfos = Workshop::getNameById($workshopIds);
        $data = [];

        $list = array_diff($plant, $workshop);
        foreach ($list as $item) {
            $belongType = $item['belong_type'];
            $belongId = $item['belong_id'];
            $userId = $item['user_id'];
            $data[$userId][] = $belongType == self::BELONG_TYPE_WORKSHOP ? $workshopInfos[$belongId] : $plantInfos[$belongId];
        }
        foreach ($data as $userId => $value) {
            $data[$userId] = implode('，', $value);
        }
        return $data;
    }
}
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
            ->select(['user_id', 'belong_id plant_id', 'p.name plant_name'])
            ->innerJoin('plant p', 'p.id = belong_id')
            ->where(['user_id' => $ids])
            ->andWhere(['belong_type' => self::BELONG_TYPE_PLANT])
            ->groupBy(['user_id', 'belong_id'])
            ->asArray()
            ->all();

        $workshop = self::find()
            ->select(['user_id', 'belong_id workshop_id', 'any_value(p.id) plant_id', 'any_value(p.name) plant_name', 'any_value(w.name) workshop_name'])
            ->innerJoin(['workshop w', 'w.id = belong_id'])
            ->innerJoin(['plant_workshop pw', 'pw.workshop_id = w.id'])
            ->innerJoin(['plant p', 'p.id = pw.plant_id'])
            ->where(['user_id' => $ids])
            ->andWhere(['belong_type' => self::BELONG_TYPE_WORKSHOP])
            ->groupBy(['user_id', 'belong_id'])
            ->asArray()
            ->all();

        $data = [];
        foreach ($plant as $item) {
            $userId = $item['user_id'];
            $data[$userId][] = [
                'plant_id' => $item['plant_id'],
                'plant_name' => $item['plant_name'],
                'workshop_id' => '',
                'workshop_name' => '',
                'full_name' => $item['plant_name']
            ];
        }

        foreach ($workshop as $item) {
            $userId = $item['user_id'];
            $data[$userId][] = [
                'plant_id' => $item['plant_id'],
                'plant_name' => $item['plant_name'],
                'workshop_id' => $item['workshop_id'],
                'workshop_name' => $item['workshop_name'],
                'full_name' => "{$item['plant_name']}-{$item['workshop_name']}"
            ];
        }

        foreach ($ids as $id) {
            if (empty($data[$id])) {
                $data[$id] = [];
            }
        }
        return $data;
    }

    /**
     * 更新分配的厂区车间
     * @param User $user
     * @param $disArr
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function distribution(User $user, $disArr)
    {
        $userId = $user->getId();
        $data = [];
        foreach ($disArr as $item) {
            if (empty($item['workshop_id'])) {
                $type = 2;
                $beongId = $item['plant_id'];
            } else {
                $type = 1;
                $beongId = $item['workshop_id'];
            }
            $data[] = [
                'user_id' => $userId,
                'belong_type' => $type,
                'belong_id' => $beongId,
            ];
        }

        $innerTransaction = \Yii::$app->db->beginTransaction();//事务开始
        try {
            UserBelong::deleteAll(['user_id' => $userId]);
            \Yii::$app->db->createCommand()->batchInsert('user_belong', ['user_id', 'belong_type', 'belong_id'], $data)->execute();
            $innerTransaction->commit();
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
            return false;
        }
        return true;
    }
}
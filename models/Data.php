<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "data".
 *
 * @property integer $id
 * @property integer $loop_id
 * @property string $time
 * @property float $mv
 * @property float $pv
 * @property float $sv
 * @property integer $mode
 */
class Data extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * 获取测量的数据
     * @param $loopId
     * @param $start
     * @param $end
     * @return array|ActiveRecord[]
     */
    public static function getDataByLoopIdAndTimeScope($loopId, $start, $end)
    {
        $list = self::find()
            ->select(['loop_id', "DATE_FORMAT(time, '%H:%i') as time", 'mv', 'pv', 'sv', 'mode'])
            ->where(['loop_id' => $loopId])
            ->andWhere(['between', 'time', $start, $end])
            ->asArray()
            ->all();
        return $list;
    }
}
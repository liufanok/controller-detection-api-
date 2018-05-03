<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "loops".
 *
 * @property integer $id
 * @property string $name
 * @property integer $workshop_id
 * @property string $create_time
 * @property string $update_time
 */
class Loops extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loops';
    }

    /**
     * 添加回路
     * @param $workshopId
     * @param $name
     * @return bool
     */
    public static function add($workshopId, $name)
    {
        $loop = new Loops();
        $loop->name = $name;
        $loop->workshop_id = $workshopId;
        return $loop->save();
    }
}
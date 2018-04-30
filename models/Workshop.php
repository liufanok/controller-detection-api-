<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "workshop".
 *
 * @property integer $id
 * @property string $name
 * @property integer $plant_id
 * @property string $create_time
 * @property string $update_time
 */
class Workshop extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'workshop';
    }
}
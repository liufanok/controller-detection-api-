<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "plant_workshop".
 *
 * @property integer $id
 * @property integer $plant_id
 * @property integer $workshop_id
 * @property string $create_time
 * @property string $update_time
 */
class PlantWorkshop extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plant_workshop';
    }
}
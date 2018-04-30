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
}
<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "plant".
 *
 * @property integer $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 */
class Plant extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plant';
    }
}
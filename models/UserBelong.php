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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_belong';
    }
}
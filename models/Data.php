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
}
<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "result".
 *
 * @property integer $id
 * @property integer $loop_id
 * @property string $loop_name
 * @property string $date
 * @property string $suggest
 * @property string $performance
 * @property float $rpi
 * @property float $osci
 * @property integer $set_time
 * @property float $dev_err
 * @property float $e_err
 * @property float $e_sf
 * @property float $sf
 * @property float $dev_sf
 * @property float $dev_pv
 * @property float $dev_mv
 * @property float $dev_sv
 * @property float $switch
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 * @property string $update_time
 */
class Result extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'result';
    }
}
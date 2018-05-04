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

    /**
     * 获取时间范围列表
     * @param $loopId
     * @param $date
     * @return array
     */
    public static function getTimeListByLoopIdAndDate($loopId, $date)
    {
        $list = self::find()
            ->select(['id', 'CONCAT(start_time, '-', end_time) time_scope'])
            ->where(['loop_id' => $loopId])
            ->andWhere(['date' => $date])
            ->asArray()
            ->all();
        return array_column($list, 'time_scope', 'id');
    }

    public static function reportDate($resultId)
    {

    }
    /**
     * 获取意见
     * @param $num
     * @return string
     */
    private static function getSuggest($num)
    {
        switch ($num) {
            case 1 : $suggest = '有效投用率太低，分析过程中的限定条件，或者控制方案是否适用。'; break;
            case 2 : $suggest = '有效投用率极低，考虑检查该控制器的可用性，或者设计方案的合理性。';break;
            case 3 : $suggest = '振荡指数在低级警告范围内，存在振荡。根据实际需求调整，如果在可接受范围，可以忽略；根据需求，决定是否需要调整PID。';break;
            case 4 : $suggest = '振荡指数在高级警告区域内，存在较强烈振荡。不可忽略，需要注意观察；根据需求，决定是否需要调整PID。';break;
            case 5 : $suggest = '存在振荡，过调回路，考虑重新整定PID';break;
            case 6 : $suggest = '有效投用率较低，振荡指数在高级警告范围内。可能是粘滞回路吗，检查阀门；阀门可能已经饱和，控制无法作用，检查阀门。';break;
            case 7 : $suggest = '控制器输出卡在上限，控制器无效。';break;
            case 8 : $suggest = '控制器输出卡在下限，控制器无效';break;
            default : $suggest = '';
        }
        return $suggest;
    }
}
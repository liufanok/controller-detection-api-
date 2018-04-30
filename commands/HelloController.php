<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    /**
     * 导入data表的数据
     * @throws \yii\db\Exception
     */
    public function actionImportData()
    {
        $db = \Yii::$app->db;
        $sql1 = "select id,name from loops";
        $loops = $db->createCommand($sql1)->queryAll();
        $loops = array_column($loops, 'name', 'id');

        $sql2 = " select * from data_20180309_0";
        $primaryData = $db->createCommand($sql2)->queryAll();

        foreach ($primaryData as $item) {
            $data = [];
            $time = "2018-03-09 " . $item['Time'];
            foreach ($loops as $id => $name) {
                $mode = $item[$name.'.MODE'];
                $mv = $item[$name.'.MV'];
                $pv = $item[$name.'.PV'];
                $sp = $item[$name.'.SP'];
                $data[] = [$id,$time,$mv,$pv,$sp,$mode];
            }
            $db->createCommand()->batchInsert("data", ['loop_id', 'time', 'mv', 'pv', 'sv', 'mode'], $data)->execute();
        }
    }
}

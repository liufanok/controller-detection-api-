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

    /**
     * 获取/搜索回路的列表
     * @param $name
     * @param $workshopId
     * @param $page
     * @param $limit
     * @return array
     */
    public static function search($name, $workshopId, $page, $limit)
    {
        $query = self::find()
            ->select(['id', 'name', 'workshop_id', 'w.name'])
            ->innerJoin('workshop w', 'w.id = loops.workshop_id')
            ->filterWhere(['like', 'name', $name])
            ->andFilterWhere(['workshop_id' => $workshopId]);
        $count = $query->count();

        $offset = ($page - 1) * $limit;
        $list = $query->offset($offset)
            ->limit($limit)
            ->asArray()
            ->orderBy(['id' => SORT_DESC])
            ->all();
        return [
            'count' => $count,
            'data' => $list,
        ];
    }
}
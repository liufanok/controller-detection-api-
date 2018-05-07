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

    /**
     * 搜索/获取厂区列表
     * @param $name
     * @param $page
     * @param $limit
     * @return array
     */
    public static function search($name, $page, $limit)
    {

        $query = self::find()
            ->select(['plant.id', 'plant.name', 'count(w.name) as workshop_count'])
            ->leftJoin('workshop w', 'w.plant_id = plant.id')
            ->filterWhere(['like', 'plant.name', $name])
            ->groupBy('plant.id');
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

    /**
     * 获取所有的厂区
     * @return array
     */
    public static function getAll()
    {
        $list = self::find()
            ->select(['id', 'name'])
            ->asArray()
            ->all();
        return $list ? $list : [];
    }

    /**
     * 添加厂区
     * @param $name
     * @return bool
     */
    public static function add($name)
    {
        $plant = new plant();
        $plant->name = $name;
        return $plant->save();
    }
}
<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "workshop".
 *
 * @property integer $id
 * @property string $name
 * @property integer $plant_id
 * @property string $create_time
 * @property string $update_time
 */
class Workshop extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'workshop';
    }

    /**
     * 获取/搜索车间信息
     * @param $name
     * @param $plantId
     * @param $page
     * @param $limit
     * @return array
     */
    public static function search($name, $plantId, $page, $limit)
    {

        $query = self::find()
            ->select(['id', 'name', 'plant_id'])
            ->filterWhere(['like', 'name', $name])
            ->andFilterWhere(['plant_id' => $plantId]);
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
     * 添加车间
     * @param $name
     * @param $plantId
     * @return bool
     */
    public static function add($name, $plantId)
    {
        $workshop = new Workshop();
        $workshop->name = $name;
        $workshop->plant_id = $plantId;
        return $workshop->save();
    }
}
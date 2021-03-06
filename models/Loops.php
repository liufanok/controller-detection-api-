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
     * 获取回路名
     * @param $loopId
     * @return string
     */
    public static function getNameById($loopId)
    {
        $loop = self::findOne($loopId);
        return empty($loop) ? '' : $loop->name;
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
            ->select(['loops.id', 'loops.name loop_name', 'workshop_id', 'w.name'])
            ->innerJoin('workshop w', 'w.id = loops.workshop_id')
            ->filterWhere(['like', 'loops.name', $name])
            ->andFilterWhere(['workshop_id' => $workshopId]);
        $count = $query->count();

        $offset = ($page - 1) * $limit;
        $list = $query->offset($offset)
            ->limit($limit)
            ->asArray()
            ->orderBy(['loops.id' => SORT_DESC])
            ->all();
        return [
            'count' => $count,
            'data' => $list,
        ];
    }

    /**
     * 根据用户获取回路的数据
     * @param User $user
     * @param $name
     * @param $workshopId
     * @param $page
     * @param $limit
     * @return array
     */
    public static function getLoopsByUser(User $user, $name, $workshopId, $page, $limit)
    {
        $role = $user->roles;
        $userId = $user->getId();
        $workshopIds = [];
        if ($role == User::ROLE_NORMAL) {
            $userBelong = UserBelong::find()
                ->select(['belong_id', 'belong_type'])
                ->where(['user_id' => $userId])
                ->asArray()
                ->all();
            $plantIds = [];
            foreach ($userBelong as $item) {
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_WORKSHOP) {
                    $workshopIds[] = $item['belong_id'];
                }
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_PLANT) {
                    $plantIds[] = $item['belong_id'];
                }
            }
            if (!empty($plantIds)) {
                $plantWorkshopIds = Workshop::find()
                    ->select(['id'])
                    ->where(['plant_id' => $plantIds])
                    ->asArray()
                    ->all();
                $plantWorkshopIds = array_column($plantWorkshopIds, 'id');
                $workshopIds = array_merge($workshopIds, $plantWorkshopIds);
            }

            if (empty($workshopIds) || ($workshopId && !in_array($workshopId, $workshopIds))) {
                return [
                    'count' => 0,
                    'data' => [],
                ];
            }
        }
        if (!empty($workshopId)) {
            $workshopIds = $workshopId;
        }

        //回路的数据
        $query = Loops::find()
            ->select(['loops.id', 'loops.name loop_name', 'w.name'])
            ->innerJoin('workshop w', 'w.id = loops.workshop_id')
            ->filterWhere(['w.id' => $workshopIds])
            ->andFilterWhere(['like', 'loops.name', $name]);
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
     * 导出的数据
     * @param User $user
     * @return array|ActiveRecord[]
     */
    public static function getAllLoopsData(User $user)
    {
        $role = $user->roles;
        $userId = $user->getId();
        $workshopIds = [];
        if ($role == User::ROLE_NORMAL) {
            $userBelong = UserBelong::find()
                ->select(['belong_id', 'belong_type'])
                ->where(['user_id' => $userId])
                ->asArray()
                ->all();
            $plantIds = [];
            foreach ($userBelong as $item) {
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_WORKSHOP) {
                    $workshopIds[] = $item['belong_id'];
                }
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_PLANT) {
                    $plantIds[] = $item['belong_id'];
                }
            }
            if (!empty($plantIds)) {
                $plantWorkshopIds = Workshop::find()
                    ->select(['id'])
                    ->where(['plant_id' => $plantIds])
                    ->asArray()
                    ->all();
                $plantWorkshopIds = array_column($plantWorkshopIds, 'id');
                $workshopIds = array_merge($workshopIds, $plantWorkshopIds);
            }

            if (empty($workshopIds)) {
                return [];
            }
        }

        //回路的数据
        $list = Loops::find()
            ->select(['loops.id', 'loops.name loop_name', 'p.name plant_name', 'w.name workshop_name'])
            ->innerJoin('workshop w', 'w.id = loops.workshop_id')
            ->innerJoin('plant_workshop pw', 'pw.workshop_id = w.id')
            ->innerJoin('plant p', 'p.id = pw.plant_id')
            ->filterWhere(['w.id' => $workshopIds])
            ->asArray()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $loopIds = array_column($list, 'id');
        $count = Result::getCountByLoopIds($loopIds);
        foreach ($list as &$item) {
            $item['report_count'] = intval($count[$item['id']]);
        }
        return $list;
    }

    /**
     * 判断一个用户是否有权限查看某个回路
     * @param User $user
     * @param $loopId
     * @return bool
     */
    public static function userHasAccess(User $user, $loopId)
    {
        if ($user->roles == User::ROLE_ADMIN) {
            return true;
        } else {
            $userId = $user->id;
            $userBelong = UserBelong::find()
                ->select(['belong_id', 'belong_type'])
                ->where(['user_id' => $userId])
                ->asArray()
                ->all();
            $plantIds = [];
            $workshopIds = [];
            foreach ($userBelong as $item) {
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_WORKSHOP) {
                    $workshopIds[] = $item['belong_id'];
                }
                if ($item['belong_type'] == UserBelong::BELONG_TYPE_PLANT) {
                    $plantIds[] = $item['belong_id'];
                }
            }
            if (!empty($plantIds)) {
                $plantWorkshopIds = Workshop::find()
                    ->select(['id'])
                    ->where(['plant_id' => $plantIds])
                    ->asArray()
                    ->all();
                $plantWorkshopIds = array_column($plantWorkshopIds, 'id');
                $workshopIds = array_unique(array_merge($workshopIds, $plantWorkshopIds));
            }

            if (empty($workshopIds)) {
                return false;
            }
            $loops = Loops::find()
                ->where(['and',['id' => $loopId], ['workshop_id' => $workshopIds]])
                ->asArray()
                ->all();
            return !empty($loops);
        }
    }
}
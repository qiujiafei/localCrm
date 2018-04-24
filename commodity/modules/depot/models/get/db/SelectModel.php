<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:20
 */

namespace commodity\modules\depot\models\get\db;
use commodity\modules\depot\models\DepotObject;
use common\ActiveRecord\DepotAR;
use yii\data\Pagination;

class SelectModel extends DepotAR
{
    /**
     * @param array $where
     * @param int $pageSize
     * @return array
     * 查找列表
     */
    public function findList($where=[],$pageSize=20)
    {
        $depotObject = new DepotObject();
        $query = $depotObject->getDepotQuery($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $list = $depotObject->getListByQuery($query,['id','depot_name','created_time','comment'],$pagination->offset,$pagination->limit);

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];

    }
}
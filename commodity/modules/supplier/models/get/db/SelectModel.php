<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 13:34
 */

namespace commodity\modules\supplier\models\get\db;

use commodity\modules\supplier\models\SupplierObject;
use common\ActiveRecord\SupplierAR;
use yii\data\Pagination;


class SelectModel extends SupplierAR
{
    /**
     * @param array $where
     * @param int $pageSize
     * @return array|\yii\db\ActiveRecord[]
     * 查询列表数据
     */
    public function findList($where=[],$pageSize=20)
    {
        $supplierObject = new SupplierObject();
        $storeId = $where['store_id'];
        unset($where['store_id']);
        $query = $supplierObject->getListSuppliersQuery($storeId,$where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $list = $supplierObject->getListSuppliersQueryPagination($query,$pagination->offset,$pagination->limit);

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }
}
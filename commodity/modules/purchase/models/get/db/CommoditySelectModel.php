<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/20
 * Time: 11:53
 * 商品查询，用于采购添加时使用
 */

namespace commodity\modules\purchase\models\get\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\DepotAR;
use yii\data\Pagination;

class CommoditySelectModel extends CommodityAR
{
    private $pagination;
    /**
     * 查询当前门店的商品列表
     * @param $where
     * @param int $pageSize
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findListByNameOfStore($where,$pageSize = 20)
    {
        $query = self::find()->where($where);

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $this->pagination = $pagination;

        return  $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->with('depot')
            ->orderBy('created_time DESC')
            ->all();
    }

    /**
     * 获取默认仓库
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return self::hasOne(DepotAR::className(),['id'=>'default_depot_id']);
    }

    public function getPagination()
    {
        return $this->pagination;
    }
}
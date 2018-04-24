<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\inventory\models\get\db;

use commodity\modules\inventory\logics\InventoryCommodityLogicModel;
use common\ActiveRecord\EmployeeUserAR;
use common\ActiveRecord\InventoryCommodityAR;
use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\DepotAR;
use common\ActiveRecord\InventoryAR;
use yii\data\Pagination;

class SelectModel extends InventoryAR
{

    /**
     * 关联查询商品信息
     *
     */
    public function getCommodities()
    {
        return $this->hasMany(CommodityAR::className(),['id' => 'commodity_id'])
            ->viaTable('{{%inventory_commodity}}',['inventory_id'=>'id']);
    }

    /**
     * 关联查询仓库信息
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(DepotAR::className(),['id' => 'depot_id']);
    }

    /**
     * 关联查询单据商品信息
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryCommodity()
    {
        return $this->hasMany(InventoryCommodityAR::className(),['inventory_id'=>'id']);
    }

    /**
     * 查询并返回一条数据
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function createOneDataById($id)
    {
        $model = self::findOne($id);
        if (null === $model) {
            throw new \Exception('未定义数据',18005);
        }
        $data = $model->toArray();
        //商品信息
        $data['inventoryCommodities'] = $model->inventoryCommodity;
        $data['commodities'] = $model->commodities;
        return $data;
    }

    /**
     * 获取采购单详情
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function getOneDataById($id)
    {
        $model = self::findOne($id);
        if (null === $model) {
            throw new \Exception('未定义数据',12006);
        }
        return $model->toArray();
    }

    /**
     * 查询列表
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public static function findList($where,$pageSize=20)
    {
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->where($where)
            ->with('user')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_time DESC')
            ->all();

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }

    /**
     * 获取该盘点单总盈亏
     * @return float|int
     */
    public function getGenerateProfitAndLoss()
    {
        return InventoryCommodityLogicModel::getProfitAndLossByInventoryId($this->id);
    }

    /**
     * 通过商品获取盈亏状态，该方法只能被自身模型调用，潜在传入盘点单ID
     * @param $commodityId
     * @return float|int
     */
    public function getGenerateProfitAndLossByCommodityId($commodityId)
    {
        return InventoryCommodityLogicModel::getProfitAndLossByCommodityId($this->id,$commodityId);
    }

    /**
     * 关联查询用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(EmployeeUserAR::className(),['id'=>'inventory_by']);
    }
}

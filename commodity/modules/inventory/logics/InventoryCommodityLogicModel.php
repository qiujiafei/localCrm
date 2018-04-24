<?php
namespace commodity\modules\inventory\logics;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityBatchAR;
use common\ActiveRecord\DepotAR;
use common\ActiveRecord\EmployeeUserAR;
use common\ActiveRecord\InventoryAR;
use common\ActiveRecord\InventoryCommodityAR;
use yii\data\Pagination;

class InventoryCommodityLogicModel extends InventoryCommodityAR
{

    /**
     * 计算当前盘点单总盈亏
     * @param $inventoryId  盘点单ID
     * @return float|int
     */
    public static function getProfitAndLossByInventoryId($inventoryId)
    {
        $where = [
            'inventory_id' => $inventoryId
        ];

        $data = self::find()->where($where)->select(['quantity','inventory_quantity'])->all();
        //盘点实际输入的数量之和
        $quantities =  array_sum(array_column($data,'quantity'));
        //盘点时，库存应该剩余的量
        $inventory_quantities = array_sum(array_column($data,'inventory_quantity'));
        $compare = $inventory_quantities <=> $quantities;
        if ($compare == 0) {
            return 0;
        }
        $diff = abs($quantities - $inventory_quantities);
        if ($compare == -1) {
            return $diff;
        }
        return 0 - $diff;
    }

    /**
     * @param $inventoryId 盘点单ID
     * @param $commodityId 商品ID
     * @return float|int
     */
    public static function getProfitAndLossByCommodityId($inventoryId,$commodityId)
    {
        $where = [
            'inventory_id' => $inventoryId,
            'commodity_id' => $commodityId
        ];
        $data = self::find()->where($where)->select(['quantity','inventory_quantity'])->one();

        if (null === $data) {
            return 0;
        }
        $data = $data->toArray();

        $compare = $data['inventory_quantity'] <=> $data['quantity'];
        if ($compare == 0) {
            return 0;
        }
        $diff = abs($data['quantity'] - $data['inventory_quantity']);
        if ($compare == -1) {
            return $diff;
        }
        return 0 - $diff;
    }

    /**
     * 获取盘点单总差额
     * @param $id
     * @return float|int|string
     */
    public static function getDiffPriceAllByInventoryId($id)
    {
        $where = [
            'inventory_id' => $id
        ];
        $data = self::find()->where($where)
            ->with('batch')
            ->all();
        return self::calculationDiffPrice($data);
    }

    /**
     * 盘点明细列表，差额计算
     * @param $inventoryId
     * @param $commodityId
     * @return float|int|string
     */
    public static function getDiffPriceByInventoryIdAndCommodityId($inventoryId,$commodityId)
    {
        $where = [
            'inventory_id' => $inventoryId,
            'commodity_id' => $commodityId
        ];
        $data = self::find()->where($where)
            ->with('batch')
            ->all();
        //价格初始值
        return self::calculationDiffPrice($data);
    }

    /**
     * 计算差额
     * @param $data
     * @return float|int|string
     */
    private static function calculationDiffPrice($data)
    {
        $price = '0.00';
        foreach($data as $model) {
            if (null === $model || null === $model->batch) {
                continue;
            }
            $diffNumber = $model->quantity - $model->inventory_quantity;
            $costPrice = $model->batch->cost_price;
            $price += $diffNumber * $costPrice;
        }
        return $price;
    }


    public function getBatch()
    {
        return $this->hasOne(CommodityBatchLogicModel::className(),['id'=>'commodity_batch_id']);
    }


    /**
     * 以盘点单条件查询点单据详情
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public static function findCommodityListForInventory($where,$pageSize=20)
    {
        $query = self::find()->alias('ic')
            ->where($where)
            ->leftJoin('{{%inventory}} i','i.id = ic.inventory_id')
            ->select('ic.*');

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->where($where)
            ->with('user')
            ->with('inventory')
            ->with('commodity')
            ->with('depot')
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
     * 关联查询用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(EmployeeUserAR::className(),['id'=>'created_by']);
    }

    /**
     * 获取盘点单主表信息
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(InventoryAR::className(),['id'=>'inventory_id']);
    }

    /**
     * 获取商品信息
     * @return \yii\db\ActiveQuery
     */
    public function getCommodity()
    {
        return $this->hasOne(CommodityAR::className(),['id'=>'commodity_id']);
    }

    /**
     * 获取仓库
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(DepotAR::className(),['id'=>'depot_id']);
    }

    /**
     * 盘盈统计
     * @param $storeId
     * @return float|int|string
     */
    public static function getInventoryProfitLists($storeId)
    {
        $query = self::find()->alias('ic')
            ->where('ic.store_id=:storeId and ic.quantity > ic.inventory_quantity',['storeId'=>$storeId])
            ->leftJoin('{{%commodity_batch}} as cb','ic.commodity_batch_id =cb.id')
            ->with('commodityBatch');

        $profitPrice = '0.00';
        foreach ($query->batch(100) as $elem){
            foreach($elem as $model){
                if($model && null !== $batch = $model->commodityBatch ) {
                    $profitPrice += $batch->cost_price * ($model->quantity - $model->inventory_quantity);
                }
            }
        }
        return $profitPrice;
    }

    /**
     * 盘亏统计
     * @param $storeId
     * @return float|int|string
     */
    public static function getInventoryLossLists($storeId)
    {
        $query = self::find()->alias('ic')
            ->where('ic.store_id=:storeId and ic.quantity < ic.inventory_quantity',['storeId'=>$storeId])
            ->leftJoin('{{%commodity_batch}} as cb','ic.commodity_batch_id =cb.id')
            ->with('commodityBatch');

        $profitPrice = '0.00';
        foreach ($query->batch(100) as $elem){
            foreach($elem as $model){
                if($model && null !== $batch = $model->commodityBatch ) {
                    $profitPrice += $batch->cost_price * ($model->quantity - $model->inventory_quantity);
                }
            }
        }
        return $profitPrice;
    }

    public function getCommodityBatch()
    {
        return $this->hasOne(CommodityBatchAR::className(),['id'=>'commodity_batch_id']);
    }
}
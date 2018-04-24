<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/28
 * Time: 11:12
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\finance\models\get\db;

use commodity\modules\supplier\models\SupplierLogicModel;
use commodity\modules\supplier\models\SupplierObject;
use common\ActiveRecord\CommodityBatchAR;
use common\ActiveRecord\PurchaseAR;
use common\ActiveRecord\SupplierAR;

class CommodityBatchSelectModel extends CommodityBatchAR
{
    /**
     * 查询采购应付列表
     * @param $storeId
     * @param $startTime  限定开始时间
     * @param $endTime    限定结束时间
     * @param null $supplierId  供应商ID
     * @return array
     * @throws \Exception
     */
    public function findListOfSupplier($storeId,$startTime,$endTime,$supplierId = null)
    {
        $supplierObject = new SupplierObject();
        //有供应商的情况
        if ($supplierId) {
            if ( ! $supplierObject->isSupplierOfStore($storeId, $supplierId)) {
                throw new \Exception('非当前门店供应商',5016);
            }

            $supplierQuery = $supplierObject->getSupplierQueryById($supplierId);
        } else {
            //供应商query对象
            $supplierQuery = $supplierObject->getSupplierOfStoreQueryByStoreId($storeId);
        }

        $list = $temp = [];

        $where = [
            'and',
            ['store_id' => $storeId],
            ['between','created_time',$startTime,$endTime]
        ];
        foreach ($supplierQuery->batch(100) as $rows) {
            foreach ($rows as $model) {
                $supplier = $model->toArray();
                $id = $supplier['id'];
                $temp['supplier_name'] = $supplier['main_name'];
                //加入供应商ID
                $where[] = ['supplier_id' => $id];
                $temp['total_price'] = $this->getSupplierPurchaseAmountOfMoney($where);
                array_pop($where);
                $list[$id] = $temp;
            }
        }

        $return = [];
        //九大爷的数据排第一个
        $jiuId = SupplierObject::JIU_SUPPLIER_ID;
        foreach ($list as $key => $value)
        {
            if ($key == $jiuId){
                array_unshift($return,$value);
                continue;
            }
            $return[] = $value;
        }
        return $return;
    }

    /**
     * 通过批次的采购单号查询采购单
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return self::hasOne(PurchaseAR::className(),['number' => 'purchase_number']);
    }

    /**
     * 查询供应商采购金额
     * @param $where
     * @return mixed|string
     */
    public function getSupplierPurchaseAmountOfMoney($where)
    {
        $query = self::find()
            ->where($where)
            ->with('purchase')
            ->with('supplier');
        $purchaseNumberArr = [];

        $total_price = 0.00;

        foreach($query->batch(100) as $rows) {

            foreach($rows as $model) {

                if (null === $model) {
                    continue;
                }
                if (in_array($model->purchase_number,$purchaseNumberArr)) {
                    continue;
                }
                //供应商采购总额
                $total_price +=  $model->purchase->settlement_price;
                $purchaseNumberArr[] = $model->purchase_number;

            }
        }

        return $total_price;
    }

    /**
     * 获取供应商信息
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return self::hasOne(SupplierAR::className(),['id' => 'supplier_id']);
    }
}
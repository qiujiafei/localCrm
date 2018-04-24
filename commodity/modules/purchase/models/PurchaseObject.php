<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/13
 * Time: 15:54
 */

namespace commodity\modules\purchase\models;

use common\ActiveRecord\PurchaseAR;

class PurchaseObject
{
    private $_data;

    /**
     * 确定该门店供应商是否有采购单据
     * @param $storeId
     * @param $supplierId
     * @param null $db
     * @return bool
     */
    public function isHaveBillOfPurchase($storeId,$supplierId,$db = null)
    {
        $where = [
            'supplier_id' => $supplierId,
            'store_id' => $storeId
        ];
        return PurchaseAR::find()->where($where)->exists($db);
    }

    /**
     * 数据对象转换成数组
     * @return array
     */
    public function toArray()
    {
        if (is_array($this->_data)) {
            foreach ($this->_data as $key => $model){
                if ($model instanceof PurchaseAR) {
                    $this->_data[$key] = $model->toArray();
                }
            }
            return $this->_data;
        }

        if ($this->_data instanceof PurchaseAR) {
            return $this->_data->toArray();
        }

        return (array) $this->_data;
    }
}
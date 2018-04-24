<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models;

use commodity\modules\purchase\models\get\db\PurchaseCommoditySelectModel;
use common\ActiveRecord\PurchaseCommodityAR;

class PurchaseCommodityLogicModel extends PurchaseCommodityAR
{

    public $total_price; //按英文意思是总价，其实是该产品采购的实际价格

    /**
     * 格式化提交的数据，使其填充必填数据
     * @param $data 提交的原始数据
     * @param array $otherFields   其他必填字段
     * @return array
     * @throws \Exception
     */
    public static function formatAddData($data,$otherFields=[])
    {
        if ( ! is_array($data) || count($data) < 1) {
            throw new \Exception('采购单中无商品，不可添加',12008);
        }

        if (count($otherFields) > 0) {
            return array_map(function ($v) use ($otherFields) {
                return array_merge($v,$otherFields);
            },$data);
        }
        return $data;
    }

    public static function filterAllowsFields($data,$allowFields=[])
    {
        $return = $temp = [];
        if (!is_array($allowFields)) {
            $allowFields = (array) $allowFields;
        }

        foreach($data as $key => $commodity) {
            foreach ($commodity as $cKey => $value) {
                if ( in_array($cKey,$allowFields) ) {
                    $temp[$cKey] = $value;
                }
            }
            $return[$key] = $temp;
        }

        return $return;
    }

    /**
     * 获取总量
     * @param $where
     * @return int|string
     */
    public static function getCount($where)
    {
        $model =  new PurchaseCommoditySelectModel();
        return $model->getCount($where);
    }

    /**
     * 获取数量
     * @param $where
     * @return mixed
     */
    public static function getQuantity($where)
    {
        $model = new PurchaseCommoditySelectModel();
        $query = $model->getQuantityQuery($where);
        $sum = $query->sum('quantity');
        return $sum ?? 0.00;
    }
}
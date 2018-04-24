<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\put\db;

use common\ActiveRecord\DamagedAR;
use commodity\modules\damagedcommodity\models\put\db\Insert as damagedcommodity;
use commodity\modules\commoditybatch\models\modify\db\Update;
use Yii;

class Insert extends DamagedAR {

    //添加
    public static function insertDamaged(array $data) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $commodity_gather = $data['commodity_gather']; //报损商品
            unset($data['commodity_gather']);
            $condition['store_id'] = $data['store_id'];
            foreach ($commodity_gather as $key => $commodity) {
                if ($key == 0) {//九大大爷商品
                    $data['number'] = self::getnumber(1, 3, 10, $condition);
                } else {
                    $data['number'] = self::getnumber(2, 3, 10, $condition);
                }
                $Insert = new self;

                foreach ($data as $k => $v) {
                    $Insert->$k = $v;
                }

                if ($Insert->save(false)) {
                    foreach ($commodity as $tab => $goods) {
                        foreach ($goods as $field => $val) {
                            $commodity[$tab]['store_id'] = $data['store_id'];
                            $commodity[$tab]['damaged_id'] = $Insert->id;
                            $commodity[$tab]['last_modified_by'] = $data['last_modified_by'];
                            $commodity[$tab]['last_modified_time'] = $data['last_modified_time'];
                            $commodity[$tab]['created_time'] = $data['created_time'];
                            $commodity[$tab]['created_by'] = $data['created_by'];
                        }

                        //更改库存
                        $modify_batch_condition['id'] = $goods['commodity_batch_id'];
                        $modify_batch['stock'] = -abs($goods['quantity']);
                        $modify_stock = Update::modifyStock($modify_batch_condition, $modify_batch);
                        if (!$modify_stock) {
                            $transaction->rollback();
                            return false;
                        }
                    }

                    //报损商品添加
                    $field_key = array_keys(current($commodity));
                    $insert_damaged_commodity = damagedcommodity::batchInsertDamagedCommodity($field_key, $commodity);

                    if (!$insert_damaged_commodity) {
                        $transaction->rollback();
                        return false;
                    }
                } else {
                    $transaction->rollback();
                    return false;
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return DamagedAR::find()->select($field)->where($condition)->asArray()->one();
    }

    /**
     * 获取编号
     * $source  来源：01为9大爷商品的领料单； 02为门店自己添加
     * $type    类型：01为采购单，02为领料单。03为报损单，04为退货单，05为盘点单。 
     * 规则：8位时间+2位来源+2位类型+10位随机数字 不允许重复 
     */
    public static function getnumber($source, $type, $len = 10, $condition) {

        $time = date('Ymd');

        $random = '';
        $str = '0123456789';
        for ($i = 0; $i < $len; $i++) {
            $random .= $str[rand(0, 9)];
        }
        $number = $time . '0' . $source . '0' . $type . $random;
        $condition['number'] = $number;
        $damaged_info = self::getField($condition, 'id');
        if ($damaged_info) {
            $number = getnumber($source, $type, $len, $condition);
        }


        return $number;
    }

}

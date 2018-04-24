<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\models\put\db;

use common\ActiveRecord\PickingAR;
use commodity\modules\pickingcommodity\models\put\db\Insert as pickingcommodity;
use commodity\modules\damaged\models\put\PutModel;
use commodity\modules\damaged\models\put\db\Insert as damaged_insert;
use commodity\modules\commoditybatch\models\modify\db\Update;
use Yii;

class Insert extends PickingAR {

    /**
     * 领料商品的集合
     * $data   
     *                      commodity_batch_id  商品批次ID  
     *                      quantity            数量
     *                      comment             备注
     */
    public static function insertPicking(array $data) {
        try {

            $insert = new self;

            foreach ($data as $k => $v) {
                $insert->$k = $v;
            }

            $insert->save(false);

            return $insert->id;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return PickingAR::find()->select($field)->where($condition)->asArray()->one();
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
        $picking_info = self::getField($condition, 'id');
        if ($picking_info) {
            $number = getnumber($source, $type, $len, $condition);
        }


        return $number;
    }

}

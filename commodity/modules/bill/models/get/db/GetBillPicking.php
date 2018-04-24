<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\get\db;

use yii\data\ActiveDataProvider;
use common\ActiveRecord\BillPickingAR;
use Yii;

class GetBillPicking extends BillPickingAR {

    
    public static function BillPickingCommodityName(array $condition) {

        return self::find()
                    ->select([
                        'f.commodity_name',
                    ])
                    ->from('crm_picking_commodity as a')
                    ->join('RIGHT JOIN', 'crm_picking As b', 'a.picking_id = b.id')
                    ->join('RIGHT JOIN', 'crm_bill_picking As c', 'b.id = c.picking_id')
                    ->join('RIGHT JOIN', 'crm_bill As d', 'c.bill_id = d.id')
                    ->join('RIGHT JOIN', 'crm_commodity_batch As e', 'a.commodity_batch_id = e.id')
                    ->join('RIGHT JOIN', 'crm_commodity As f', 'f.id = e.commodity_id')
                    ->where($condition)
                    ->asArray()
                    ->all();
    }

}

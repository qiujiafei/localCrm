<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\interfaces;

use commodity\modules\damagedcommodity\models\get\db\Select;
use commodity\modules\commodityManage\models\interfaces\DamagedCheckInterface;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class DamagedCommodityCheck implements DamagedCheckInterface {

    /**
     * @param id 商品id
     * @return boolean 
     */
    public function hasDamaged($commodityId) {
        //获取门店ID
        $storeId = AccessTokenAuthentication::getUser(true);
        $i = 0;
        $condition[0] = 'and';
        $i++;
        $condition[$i] = 'a.store_id=' . $storeId;
        $i++;
        $condition[$i] = 'b.status=' . 0;
        $i++;
        $condition[$i] = 'a.commodity_id=' . $commodityId;
        $result=Select::getall('', '', $condition);
        if ($result->count) {
            return true;
        } else {
            return false;
        }
    }

}

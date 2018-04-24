<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\delete\db;

use common\ActiveRecord\CommodityAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use commodity\modules\commodityManage\models\get\db\Select;
use commodity\modules\commodityManage\models\judgment\IsClean;

class DeleteCommodity extends CommodityAR
{
    const COMMODITY_NOT_FOUND = 0;
    const COMMODITYS_FORMAT_ERROR = 1;
    const COMMODITY_NOT_CLEAN = 2;
    
    public static function deleteOne($id)
    {
        $commodity = Select::getOneWithObject($id);
        if($commodity && (new IsClean($id))()) {
            return $commodity->delete();
        }
        
        throw new \Exception(self::COMMODITY_NOT_FOUND);
    }
    
    public static function deleteBatch($commoditys)
    {
        foreach($commoditys as $commodity) {
            if((new IsClean($commodity))()) {
                self::deleteOne($commodity);
            } else {
                throw new \Exception(self::COMMODITY_NOT_CLEAN);
            }
        }
        return true;
    }    
}


<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\CommodityPropertyAR;
use Yii;

class GetPropertyName extends CommodityPropertyAR
{
    public function __invoke($id)
    {
        $property = static::findOne($id);
        
        if(!$property) {
            return false;
        }
        
        return $property->name;
    }
}
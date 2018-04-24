<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\CommodityPropertyAR;
use Yii;

class GetIsProperty extends CommodityPropertyAR
{
    public function __invoke($name)
    {
        return static::find()->where(['property_name' => $name])->exists();
    }
}
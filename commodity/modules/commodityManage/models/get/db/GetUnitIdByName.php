<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\UnitAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;

class GetUnitIdByName extends UnitAR
{
    const ERROR_CLASSIFICATION_NOT_FOUND = 0;
    
    public function __invoke($name)
    {
        $unit = self::find()
                ->select('id')
                ->where([
                    'unit_name' => $name,
                    'store_id' => AccessTokenAuthentication::getUser(true)
                ])->one();
        if(! $unit) {
            throw new \Exception(self::ERROR_CLASSIFICATION_NOT_FOUND);
        }
        return $unit->id;
    }
}

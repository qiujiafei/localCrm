<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\ClassificationAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;

class GetClassificationIdByName extends ClassificationAR
{
    const ERROR_CLASSIFICATION_NOT_FOUND = 0;
    
    public function __invoke($name)
    {
        $classification = self::find()
                ->select('id')
                ->where([
                    'classification_name' => $name,
                    'store_id' => AccessTokenAuthentication::getUser(true)
                ])->one();
        if(! $classification) {
            throw new \Exception(self::ERROR_CLASSIFICATION_NOT_FOUND);
        }
        return $classification->id;
    }
}
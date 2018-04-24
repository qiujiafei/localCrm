<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\ClassificationAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class GetIsClassification extends ClassificationAR
{
    public function __invoke($name)
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        return static::find()->where(['classification_name' => $name, 'store_id' => $storeId])->exists();
    }
}
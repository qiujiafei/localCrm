<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\get\db;

use common\ActiveRecord\ServiceClassificationAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends ServiceClassificationAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'classification_name', //must modifed
                            'depth',
                            'parent_id',
                            'comment',
                            'status',
                            'store_id',
                            'created_by',
                            'created_time',
//                            'last_modified_by',
//                            'last_modified_time',
                        ])
                        ->from('crm_service_classification')
                        ->where([
                            'id' => $id,
                        ])
                        ->asArray()
                        ->all();
    }
    
    public static function getall(array $condition = array(), $field = '*') {
        
        return ServiceClassificationAR::find()->select($field)->where($condition)->asarray()->all();
        
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

    public static function verifyStoreId() {

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }
    }

}

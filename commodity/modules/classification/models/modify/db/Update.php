<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\modify\db;

use common\ActiveRecord\ClassificationAR;
use common\ActiveRecord\CommodityAR;
use Yii;

class Update extends ClassificationAR {

    //更改
    public static function modifyClassification(array $condition, array $modify_classify_data) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = ClassificationAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_classify_data as $k => $v) {
                $modify->$k = $v;
            }
            if ($modify->save() === false) {
                $transaction->rollback();
                return false;
            } else {
                $classification_name = array_key_exists('classification_name', $modify_classify_data) ? $modify_classify_data['classification_name'] : '';
                if ($classification_name) {
                    $condition['classification_id'] = $condition['id'];
                    unset($condition['id']);
 
                    $modify_commodity_data['classification_name'] = $classification_name;
                    
                    $commodity_modify = CommodityAR::updateAll($modify_commodity_data, $condition);
                    if ($commodity_modify === false) {
                        $transaction->rollback();
                        return false;
                    }
                }
                $transaction->commit();
                return true;
            }
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }

}

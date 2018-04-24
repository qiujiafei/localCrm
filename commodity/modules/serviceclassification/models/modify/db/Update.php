<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\modify\db;

use common\ActiveRecord\ServiceAR;
use common\ActiveRecord\ServiceClassificationAR;
use Yii;

class Update extends ServiceClassificationAR {

    //更改
    public static function modifyServiceClassification(array $condition, array $modify_data) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = ServiceClassificationAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                $transaction->rollback();
                return false;
            } else { 
                $classification_name = array_key_exists('classification_name', $modify_data) ? $modify_data['classification_name'] : '';
               
                if ($classification_name) {   
                    $condition['service_claasification_id'] = $condition['id'];
                    unset($condition['id']);
                
                    $service['service_claasification_name'] = $classification_name;

                    $service_modify = ServiceAR::updateAll($service, $condition);
                    if ($service_modify === false) {
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

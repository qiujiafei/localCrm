<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\del\db;

use common\ActiveRecord\ClassificationAR;
use Yii;

class Del extends ClassificationAR {

     //删除
    public static function delClassification(array $condition) {

        $del= ClassificationAR::deleteAll($condition);
        if ($del<0) {
             throw new \Exception('分类删除失败', 4006);
              return false;
        }
        return true;
    }
    
     //获取子类id
    public static function getChildId(array $condition, $field, $id = array()) {
        
        $ids = '';
        $ids_list = ClassificationAR::find()->select($field)->where($condition)->asArray()->all();
        
        $ids .=',' . implode($id, ',');
        if ($ids_list) {
            foreach ($ids_list as $key => $value) {
                $ids .=',' . $value['id'];
                $new_condition['parent_id'] = $value['id'];
                $new_condition['store_id'] = $condition['store_id'];
                $ids .=',' . self::getChildId($new_condition, 'id');
            }
        }
        return $ids;
        
    }

}

<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\del\db;

use common\ActiveRecord\ServiceClassificationAR;
use Yii;

class Del extends ServiceClassificationAR {

    //删除
    public static function delServiceClassification(array $condition) {

        $employeetype = ServiceClassificationAR::deleteAll($condition);
        if ($employeetype < 0) {
            throw new \Exception('服务项目分类删除失败', 9016);
            return false;
        }
        return true;
    }

    //获取子类id
    public static function getChildId(array $condition, $field, $id = array()) {
        $ids = '';
        $ids_list = ServiceClassificationAR::find()->select('id')->where($condition)->asarray()->all();
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

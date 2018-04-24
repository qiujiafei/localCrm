<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\get;

use common\models\Model as CommonModel;
use commodity\modules\classification\models\get\db\Select;
use commodity\modules\classification\models\put\PutModel;
use commodity\modules\classification\models\put\db\Insert;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETALL = 'action_getall';

    public $token;
    public $parent_id;
    public $depth;

    public function scenarios() {
        return [
            self::ACTION_GETALL => [ 'depth', 'parent_id', 'token'],
        ];
    }

    public function rules() {
        return [
            ['parent_id', 'integer', 'message' => 4009],
            ['parent_id', 'default', 'value' => -1],
        ];
    }

    /**
     * 分类列表(根据目录级别和父id获取)
     */
    public function actionGetall() {

        try {

            $condition = array();
            $depth = $this->depth;
            $parent_id = $this->parent_id;
            if ($parent_id == -1 && $depth == 1) {
                $condition['id'] = 1;
            }

            $condition['depth'] = $depth? : '';
            $condition['parent_id'] = $parent_id != -1 ? $parent_id : '';

            $condition = array_filter($condition, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });

            $userIdentity = PutModel::verifyUser();
         
            if($depth!=array(1)){
                $condition['store_id'] = current($userIdentity)['store_id'];
            }
           
            $field = 'id,classification_name,depth,parent_id,comment,status,created_by,created_time';
            $list = Select::getAll($condition, $field);
            foreach ($list as $key => $value) {
                if ($value['parent_id'] > 0) {
                    $parent_condition['id'] = $value['parent_id'];
                    $info = Insert::getField($parent_condition, 'classification_name');
                    $list[$key]['parent_name'] = $info['classification_name'];
                } else {
                    $list[$key]['parent_name'] = $value['classification_name'];
                }
            }
            return $list;
        } catch (\Exception $ex) {

            $this->addError('getall', 4007);
            return false;
        }
    }

}

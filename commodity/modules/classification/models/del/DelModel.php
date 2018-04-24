<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\classification\models\del\db\Del;
use commodity\modules\classification\models\del\db\Veyrify;
use commodity\modules\classification\models\put\PutModel;

class DelModel extends CommonModel {

    const ACTION_DEL = 'action_del';

    public $token;
    public $id;

    public function scenarios() {
        return [
            self::ACTION_DEL => ['id', 'token']
        ];
    }

    public function rules() {
        return [
            [
                ['id', 'token'],
                'required',
                'message' => 2004,
            ],
        ];
    }

    /**
     * 删除分类
     */
    public function actionDel() {

        try {
           

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $condition['parent_id'] = $parent_id = $this->id;
            $condition['store_id'] = $verify_condition['store_id'] = current($userIdentity)['store_id'];
            $condition['status'] = $verify_condition['status'] = 1;

            //获取要删除的集合
            $ids_del = Del::getChildId($condition, 'id', $parent_id);
            
            $ids_del = array_filter(explode(',', $ids_del), function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
            
            //判断分类父级及所有子类下有没有商品
            $verify_condition['classification_id'] = $ids_del;
            if (!Veyrify::verifyClassCommodity($verify_condition)) {
                throw new \Exception('分类有对应的商品，不能做删除操作', 4005);
            }
           
            //删除动作
            unset($condition['parent_id']);
            $condition['id'] = $ids_del;

            if (Del::delClassification($condition)) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 4005) {
                $this->addError('remove', 4005);
                return false;
            } elseif ($ex->getCode() === 3003) {
                $this->addError('remove', 3003);
                return false;
            } else {
                $this->addError('remove', 4006);
                return false;
            }
        }
    }

}

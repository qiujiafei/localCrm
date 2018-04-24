<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\serviceclassification\models\del\db\Del;
use commodity\modules\serviceclassification\models\del\db\Veyrify;
use commodity\modules\serviceclassification\models\put\PutModel;

class DelModel extends CommonModel {

    const ACTION_DEL = 'action_del';

    public $token;
    public $id;

    public function scenarios() {
        return [
            self::ACTION_DEL => [
                'id', 'token'
            ],
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

    public function actionDel() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $condition['parent_id'] = $parent_id = $this->id;
            $condition['store_id'] = $verify_condition['store_id'] = current($userIdentity)['store_id'];

            //获取要删除的集合
            $ids_del = Del::getChildId($condition, 'id', $parent_id);

            $ids_del = array_filter(explode(',', $ids_del));

            //判断分类父级及所有子类下有没有商品
            $verify_condition['service_claasification_id'] = $ids_del;
            if (!Veyrify::veyrifyService($verify_condition)) {
                throw new \Exception('分类有对应的商品，不能做删除操作', 9015);
            }

            //删除动作
            unset($condition['parent_id']);
            $condition['id'] = $ids_del;

            if (Del::delServiceClassification($condition)) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 9015) {
                $this->addError('remove', 9015);
                return false;
            } elseif ($ex->getCode() === 3003) {
                $this->addError('remove', 3003);
                return false;
            } else {
                $this->addError('remove', 9016);
                return false;
            }
        }
    }

}

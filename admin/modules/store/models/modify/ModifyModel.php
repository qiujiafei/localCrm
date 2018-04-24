<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\models\modify;

use common\models\Model;
use admin\modules\store\models\modify\db\Update;
use admin\modules\store\models\put\PutModel;
use common\exceptions;
use Yii;

class ModifyModel extends Model {

    const ACTION_FORBID = 'action_forbid';
    const ACTION_USING = 'action_using';

    public $id;
    public $comment;

    public function scenarios() {
        return [
            self::ACTION_FORBID => [
                'id', 'comment'
            ],
            self::ACTION_USING => [
                'id',
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['id'],
                'required',
                'message' => 2004,
            ],
            ['comment', 'string', 'length' => [0, 100], 'tooLong' => 11009], //备注
        ];
    }

    /**
     * 门店禁止
     */
    public function actionForbid() {

        try {

            $condition['status'] = 1;
            $condition['id'] = $this->id;

            $modify_condition = self::getCondition($condition);

            $modify_data['status'] = 2;
            $modify_data['last_admin_modified_by'] = self::getUserId();
            $modify_data['last_modified_time'] = date('Y-m-d H:i:s');
            $modify_data['comment'] = $this->comment;
           
            //更改操作
            Update::modifyAllEmployeeUser($modify_condition, $modify_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 11005) {
                $this->addError('forbid', 11005);
                return false;
            } elseif ($ex->getCode() === 11006) {
                $this->addError('forbid', 11006);
                return false;
            } else {
                $this->addError('forbid', 11008);
                return false;
            }
        }
    }

    //获取门店的相关用户(包含要操作的用户)
    public static function getUserAccounts(array $condition, $id = array()) {

        $ids = '';
        $ids_list = Update::getEmployeeUserAll($condition, 'id');

        $ids .=',' . implode($id, ',');
        if ($ids_list) {
            foreach ($ids_list as $key => $value) {
                $ids .=',' . $value['id'];
                $new_condition['created_by'] = $value['id'];
                $ids .=',' . self::getUserAccounts($new_condition);
            }
        }
        return $ids;
    }

    public static function getCondition($condition) {

        if (!Update::getEmployeeUserField($condition, 'id')) {
            throw new \Exception('无效参数', 11005);
        }

        $id = $condition['id'];
        $condition['created_by'] = $id;
        unset($condition['id']);

        $ids = self::getUserAccounts($condition, [$id]);

        $new_condition['status'] = $condition['status'];
        $new_condition['id'] = array_filter(explode(',', $ids), function ($v) {
            if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                return false;
            }
            return true;
        });

        return $new_condition;
    }

    /**
     * 门店启用
     */
    public function actionUsing() {

        try {

            $condition['status'] = 2;
            $condition['id'] = $this->id;

            $modify_condition = self::getCondition($condition);

            $modify_data['status'] = 1;
            $modify_data['last_admin_modified_by'] = self::getUserId();
            $modify_data['last_modified_time'] = date('Y-m-d H:i:s');

            //更改操作
            Update::modifyAllEmployeeUser($modify_condition, $modify_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 11005) {
                $this->addError('forbid', 11005);
                return false;
            } elseif ($ex->getCode() === 11006) {
                $this->addError('forbid', 11006);
                return false;
            } else {
                $this->addError('forbid', 11008);
                return false;
            }
        }
    }

    public static function verifyUser() {
        if (!$userIdentity = self::getUser()) {
            throw new \Exception(sprintf(
                    "Can not found user identity in %s.", __METHOD__
            ));
        }
        return $userIdentity;
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }
    
    
    public static function getUserId() {
        return current(self::verifyUser())['id'];
    }

}

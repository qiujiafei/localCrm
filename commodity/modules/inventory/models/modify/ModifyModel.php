<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\inventory\models\modify;

use Yii;
use common\models\Model as CommonModel;


class ModifyModel extends CommonModel
{
    //编辑
    const ACTION_EDIT = 'action_edit';

    public function scenarios() {
        return [
            self::ACTION_EDIT => [
                'id'
            ]
        ];
    }

    public function rules() {
        return [
        ];
    }


    public function actionEdit()
    {
        //获取用户信息
        $user = AccessTokenAuthentication::getUser();
        try {
           return ['正在开发中......'];
        } catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

}

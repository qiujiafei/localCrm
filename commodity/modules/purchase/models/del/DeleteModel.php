<?php

/**
 * CRM system for 9daye
 * 
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models\del;

use common\models\Model as CommonModel;

class DeleteModel extends CommonModel
{
    const ACTION_INDEX = 'action_index';

    public $token;

    public function rules()
    {
        return [];
    }

    public function scenarios()
    {
        return [
            self::ACTION_INDEX => [
                'token'
            ],

        ];
    }

    public function actionIndex()
    {
        return [];
    }
}

<?php

/**
 * CRM system for 9daye
 * 
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\inventory\models\del;

use common\models\Model as CommonModel;

class DeleteModel extends CommonModel
{

    public $token;

    public function rules()
    {
        return [];
    }

    public function scenarios()
    {
        return [
        ];
    }

    public function actionIndex()
    {
        return [];
    }
}

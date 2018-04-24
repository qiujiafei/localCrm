<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\purchase\models\modify\ModifyModel;

class ModifyController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'edit' => [
            'scenario' => ModifyModel::ACTION_EDIT,
            'method' => 'post',
            'convert' => false,
        ],
        'invalid' => [
            'scenario' => ModifyModel::ACTION_INVALID,
            'method' => 'post',
            'convert' => false,
        ],

        '_model' => ModifyModel::class,
    ];
    protected $access = [
        'edit' => ['@', 'post'],
        'invalid' => ['@','post']
    ];

}

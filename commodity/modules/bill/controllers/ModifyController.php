<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\bill\models\modify\ModifyModel;

class ModifyController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'modify' => [
            'scenario' => ModifyModel::ACTION_MODIFY,
            'method' => 'post',
            'convert' => false,
        ],
        'account' => [
            'scenario' => ModifyModel::ACTION_ACCOUNT,
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
        'modify' => ['@', 'post'],
        'account' => ['@', 'post'],
        'invalid' => ['@', 'post'],
    ];

}

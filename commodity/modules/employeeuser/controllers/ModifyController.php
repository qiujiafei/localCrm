<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\employeeuser\models\modify\ModifyModel;

class ModifyController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'modify' => [
            'scenario' => ModifyModel::ACTION_MODIFY,
            'method' => 'post',
            'convert' => false,
        ],
        'stop' => [
            'scenario' => ModifyModel::ACTION_STOP,
            'method' => 'post',
            'convert' => false,
        ],
         'start' => [
            'scenario' => ModifyModel::ACTION_START,
            'method' => 'post',
            'convert' => false,
        ],
            '_model' => ModifyModel::class,
    ];
    protected $access = [
        'modify' => ['@', 'post'],
        'stop' => ['@', 'post'],
        'start' => ['@', 'post'],
    ];

}

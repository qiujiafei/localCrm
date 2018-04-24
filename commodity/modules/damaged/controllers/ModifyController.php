<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\damaged\models\modify\ModifyModel;

class ModifyController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
         'invalid' => [
            'scenario' => ModifyModel::ACTION_INVALID,
            'method' => 'post',
            'convert' => false,
        ],
            '_model' => ModifyModel::class,
    ];
    protected $access = [
        'invalid' => ['@', 'post'],
    ];

}

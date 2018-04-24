<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\commodityUnit\models\modify\ModifyModel;

class ModifyController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'modify' => [
            'scenario' => ModifyModel::ACTION_MODIFY,
            'method' => 'post',
            'convert' => false,
        ],
            '_model' => ModifyModel::class,
    ];
    protected $access = [
        'modify' => ['@', 'post'],
    ];

}

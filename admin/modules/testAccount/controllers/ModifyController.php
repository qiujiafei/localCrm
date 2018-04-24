<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace admin\modules\testAccount\controllers;

use common\controllers\Controller as CommonController;
use admin\modules\testAccount\models\modify\ModifyModel;

class ModifyController extends CommonController
{
    public $enableCsrfValidation = false;
    
    protected $actionUsingDefaultProcess = [
        'modify' => [
            'scenario' => ModifyModel::ACTION_MODIFY,
            'method' => 'post',
            'convert' => false,
        ],
        'forbid' => [
            'scenario' => ModifyModel::ACTION_FORBID,
            'method' => 'post',
            'convert' => false,
        ],
         'using' => [
            'scenario' => ModifyModel::ACTION_USING,
            'method' => 'post',
            'convert' => false,
        ],
            '_model' => ModifyModel::class,
    ];
    protected $access = [
        'modify' => ['@', 'post'],
        'forbid' => ['@', 'post'],
        'using' => ['@', 'post'],
    ];
}

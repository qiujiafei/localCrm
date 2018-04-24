<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace admin\modules\store\controllers;

use common\controllers\Controller as CommonController;
use admin\modules\store\models\modify\ModifyModel;

class ModifyController extends CommonController
{
    public $enableCsrfValidation = false;
    
    protected $actionUsingDefaultProcess = [
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
        'forbid' => ['@', 'post'],
        'using' => ['@', 'post'],
    ];
}

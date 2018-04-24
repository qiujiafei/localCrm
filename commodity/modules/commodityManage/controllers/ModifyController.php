<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\commodityManage\models\modify\ModifyModel;

class ModifyController extends CommonController
{
    public $enableCsrfValidation = false;
    
    protected $actionUsingDefaultProcess = [
        'update' => [
            'scenario' => ModifyModel::ACTION_UPDATE,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => ModifyModel::class,
    ];
    
    protected $access = [
        'update' => ['@', 'post'],
    ];
}

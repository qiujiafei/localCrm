<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use admin\modules\store\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
       
        'get-accredit-all' => [
            'scenario' => GetModel::ACTION_GETACCREDITALL,
            'method' => 'get',
            'convert' => false,
        ],
         'get-forbidden-all' => [
            'scenario' => GetModel::ACTION_GETFORBIDDENALL,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => GetModel::class,
    ];
    protected $access = [
        'get-accredit-all' => ['@', 'get'],
        'get-forbidden-all' => ['@', 'get'],
    ];
}

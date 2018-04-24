<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\sprider\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use admin\modules\sprider\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
       
        'auto-home-data' => [
            'scenario' => GetModel::ACTION_GETAUTOHOMEDATA,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => GetModel::class,
    ];
    protected $access = [
        'auto-home-data' => ['', 'get'],
    ];
}

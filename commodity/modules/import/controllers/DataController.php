<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\import\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\import\models\data\DataModel;


class DataController extends CommonController
{
    public $enableCsrfValidation = false;
    
    protected $actionUsingDefaultProcess = [
        'customer' => [
            'scenario' => DataModel::ACTION_CUSTORMER,
            'method' => 'post',
            'convert' => false,
        ],
         'service' => [
            'scenario' => DataModel::ACTION_SERVICE,
            'method' => 'post',
            'convert' => false,
        ],
         'commodity' => [
            'scenario' => DataModel::ACTION_COMMODITY,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DataModel::class,
    ];
    
    protected $access = [
        'customer' => ['@', 'post'],
        'service' => ['@', 'post'],
        'commodity' => ['@', 'post'],
    ];
}



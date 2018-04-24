<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\customerinfomation\models\modify\ModifyModel;

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
        'open' => [
            'scenario' => ModifyModel::ACTION_OPEN,
            'method' => 'post',
            'convert' => false,
        ],
        'nomember' => [
            'scenario' => ModifyModel::ACTION_NOMEMBER,
            'method' => 'post',
            'convert' => false,
        ],
        'ismember' => [
            'scenario' => ModifyModel::ACTION_ISMEMBER,
            'method' => 'post',
            'convert' => false,
        ],
            '_model' => ModifyModel::class,
    ];
    protected $access = [
        'modify' => ['@', 'post'],
        'stop' => ['@', 'post'],
        'open' => ['@', 'post'],
        'nomember' => ['@', 'post'],
        'ismember' => ['@', 'post'],
    ];

}

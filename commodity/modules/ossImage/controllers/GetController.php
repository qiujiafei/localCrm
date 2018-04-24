<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\ossImage\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\ossImage\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'oss-permission' => [
            'scenario' => GetModel::ACTION_OSS_PERMISSION,
            'convert' => false,
        ],
        '_model' => GetModel::class,
    ];
    protected $access = [
        'oss-permission' => ['@', 'get'],
    ];
}

<?php
namespace commodity\modules\supplier\controllers;

use commodity\modules\supplier\models\modify\ModifyModel;
use Yii;
use common\controllers\Controller as CommonController;

class ModifyController extends CommonController
{
    public $enableCsrfValidation = false;

    protected $actionUsingDefaultProcess = [

        'edit' => [
            'scenario' => ModifyModel::ACTION_EDIT,
            'method' => 'post',
            'convert' => false,
        ],
        'status' => [
            'scenario' => ModifyModel::ACTION_STATUS,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => ModifyModel::class
    ];

    protected $access = [
        'edit'   => ['@','post'],
        'status'   => ['@','post'],
    ];
}
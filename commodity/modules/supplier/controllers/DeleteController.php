<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 17:28
 */

namespace commodity\modules\supplier\controllers;

use commodity\modules\supplier\models\delete\DeleteModel;
use Yii;
use common\controllers\Controller as CommonController;

class DeleteController extends CommonController
{
    public $enableCsrfValidation = false;

    protected $actionUsingDefaultProcess = [
        'index' => [
            'scenario' => DeleteModel::ACTION_DELETE,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DeleteModel::class
    ];

    protected $access = [
        'index' => ['@','post']
    ];

}
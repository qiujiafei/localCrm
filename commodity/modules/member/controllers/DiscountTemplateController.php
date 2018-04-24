<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */
namespace commodity\modules\member\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\member\models\cardTemplate\DiscountTemplateModel;

class DiscountTemplateController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'get-one' => [
            'scenario' => DiscountTemplateModel::ACTION_GET_ONE,
            'method' => 'get',
            'convert' => false,
        ],
        'get-all' => [
            'scenario' => DiscountTemplateModel::ACTION_GET_ALL,
            'method' => 'get',
            'convert' => false,
        ],
        'put-one' => [
            'scenario' => DiscountTemplateModel::ACTION_PUT_ONE,
            'method' => 'post',
            'convert' => false,
        ],
        'set-one' => [
            'scenario' => DiscountTemplateModel::ACTION_SET_ONE,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DiscountTemplateModel::class,
    ];
    protected $access = [
        'get-one' => ['@', 'get'],
        'get-all' => ['@', 'get'],
        'put-one' => ['@', 'post'],
        'set-one' => ['@', 'post'],
    ];

}
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

class DiscountTemplateServiceController extends CommonController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'put-batch' => [
            'scenario' => DiscountTemplateModel::ACTION_PUT_BATCH,
            'method' => 'post',
            'convert' => false,
        ],
        'get-batch' => [
            'scenario' => DiscountTemplateModel::ACTION_GET_BATCH,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DiscountTemplateModel::class,
    ];
    protected $access = [
        'put-batch' => ['@', 'post'],
        'get-batch' => ['@', 'get'],
    ];
}
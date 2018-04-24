<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\carbasicinformation\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\carbasicinformation\models\get\GetModel;

class GetController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
      
        'getCarNumberAlphabet' => [
            'scenario' => GetModel::ACTION_GETCARNUMBERALPHABET,
            'method' => 'get',
            'convert' => false,
        ],
        'getCarNumberProvince' => [
            'scenario' => GetModel::ACTION_GETCARNUMBERPROVINCE,
            'method' => 'get',
            'convert' => false,
        ],
        'getCarAlphabetHome' => [
            'scenario' => GetModel::ACTION_GETCARALPHABETHOME,
            'method' => 'get',
            'convert' => false,
        ],
        'getCarBrandHome' => [
            'scenario' => GetModel::ACTION_GETCARBRANDHOME,
            'method' => 'get',
            'convert' => false,
        ],
        'getCarTypeHome' => [
            'scenario' => GetModel::ACTION_GETCARTYPEHOME,
            'method' => 'get',
            'convert' => false,
        ],
        'getCarStyleHome' => [
            'scenario' => GetModel::ACTION_GETCARSTYLEHOME,
            'method' => 'get',
            'convert' => false,
        ],
            '_model' => GetModel::class,
    ];
    protected $access = [
        'getCarNumberAlphabet' => ['@', 'get'],
        'getCarNumberProvince' => ['@', 'get'],
        'getCarAlphabetHome' => ['@', 'get'],
        'getCarBrandHome' => ['@', 'get'],
        'getCarTypeHome' => ['@', 'get'],
        'getCarStyleHome' => ['@', 'get'],
    ];

}

<?php

namespace commodity\modules\bill\controllers;

use commodity\modules\bill\models\get\SearchModel;
use common\controllers\Controller as CommonController;

class SearchController extends CommonController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'customers' => [
            'scenario' => SearchModel::ACTION_CUSTOMERS,
            'method' => 'get',
            'convert' => false,
        ],
        'services' => [
            'scenario' => SearchModel::ACTION_SERVICES,
            'method' => 'get',
            'convert' => false,
        ],
        'commodities' => [
            'scenario' => SearchModel::ACTION_COMMODITIES,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => SearchModel::class,
    ];
    protected $access = [
        'customers' => ['@', 'get'],
        'services' => ['@', 'get'],
        'commodities' => ['@', 'get'],
    ];
}

<?php
/**
 * CRM system for 9daye
 * 统计
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\controllers;
use common\controllers\Controller as CommonController;
use commodity\modules\purchase\models\get\StatisticsModel;

class StatisticsController extends CommonController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'today' => [
            'scenario' => StatisticsModel::ACTION_TODAY,
            'method' => 'get',
            'convert' => false,
        ],
        'month' => [
            'scenario' => StatisticsModel::ACTION_MONTH,
            'method' => 'get',
            'convert' => false,
        ],
        'total' => [
            'scenario' => StatisticsModel::ACTION_TOTAL,
            'method' => 'get',
            'convert' => false,
        ],
        'all' => [
            'scenario' => StatisticsModel::ACTION_ALL,
            'method' => 'get',
            'convert' => false,
        ],

        '_model' => StatisticsModel::class,
    ];

    protected $access = [
        'today' => ['@', 'get'],
        'month' => ['@', 'get'],
        'total' => ['@', 'get'],
        'all' => ['@','get'],
    ];
}
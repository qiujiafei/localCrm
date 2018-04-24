<?php
/**
 * CRM system for 9daye
 *
 * @author: wj <wangjie@9daye.com.cn>
 */
namespace commodity\modules\memberPoint\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\memberPoint\models\memberPoint\MemberPointModel;

class PointController extends CommonController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'count-block' => [
            'scenario' => MemberPointModel::ACTION_COUNT_BLOCK,
            'method' => 'get',
            'convert' => false,
        ],
        'put-rate' => [
            'scenario' => MemberPointModel::ACTION_PUT_RATE,
            'method' => 'post',
            'convert' => false,
        ],
        'get-member-info' => [
            'scenario' => MemberPointModel::ACTION_GET_MEMBER_INFO,
            'method' => 'get',
            'convert' => false,
        ],

        '_model' => MemberPointModel::class,
    ];
    protected $access = [
        'count-block' => ['@', 'get'],
        'put-rate' => ['@', 'post'],
        'get-member-info' => ['@', 'get'],

    ];

}
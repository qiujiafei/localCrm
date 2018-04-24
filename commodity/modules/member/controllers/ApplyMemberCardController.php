<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */
namespace commodity\modules\member\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\member\models\ApplyMemberCard\ApplyMemberCardModel;

class ApplyMemberCardController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'apply-member-card' => [
            'scenario' => ApplyMemberCardModel::ACTION_APPLY_MEMBER_CARD,
            'method' => 'post',
            'convert' => false,
        ],

        '_model' => ApplyMemberCardModel::class,
    ];
    protected $access = [
        'apply-member-card' => ['@', 'post']
    ];

}
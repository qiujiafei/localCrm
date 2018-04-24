<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\authentication\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use common\controllers\Controller;
//use yii\web\Controller;
use commodity\modules\authentication\models\AccountLoginModel;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;

class AccountController extends Controller
{

    public $enableCsrfValidation = false;

    protected $access = [
        'login'     => [null, 'post'],
        'logout'    => ['@', 'get'],
        'is-login'  => [null, 'get'],
        'captcha'   => [null,'get']
    ];

    protected $actionUsingDefaultProcess = [
        'login' => [
            'scenario'  => AccountLoginModel::ACTION_LOGIN,
            'method'    => 'post',
            'convert'   => false,
        ],
        'logout' => [
            'scenario'  => AccountLoginModel::ACTION_LOGOUT,
            'method'    => 'get',
            'convert'   => false,
        ],
        'is-login' => [
            'scenario'  => AccountLoginModel::ACTION_IS_LOGIN,
            'method'    => 'get',
            'convert'   => false,
        ],
        'captcha' => [
            'scenario'  => AccountLoginModel::ACTION_CAPTCHA,
            'method'    => 'get',
            'convert'   => false,
        ],
        '_model' => AccountLoginModel::class,
    ];

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'maxLength' => 4,
                'minLength' => 4,
            ],
        ];
    }
}

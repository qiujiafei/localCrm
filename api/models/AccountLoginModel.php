<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace api\models;

use common\models\Model as CommonModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class AccountLoginModel extends CommonModel
{
    const ACTION_LOGIN = 'action_login';
    const ACTION_LOGOUT = 'action_logout';
    const ACTION_IS_LOGIN = 'action_is_login';
    const ACTION_CAPTCHA = 'action_captcha';

    public $username;
    public $passwd;
    public $token;
    public $verifyCode;
    
    public function rules()
    {
        return [
            [
                ['username', 'passwd', 'token'],
                'required',
                'message' => 2004
            ],
            //['verifyCode','required','message'=>1002],
            //['verifyCode', 'captcha','captchaAction'=>'/authentication/account/captcha','message'=>1003,'on'=>self::ACTION_LOGIN],
        ];
    }
    
    public function scenarios(){
        return [
            self::ACTION_LOGIN => ['username', 'passwd','verifyCode'],
            self::ACTION_IS_LOGIN => ['token'],
            self::ACTION_LOGOUT => [],
        ];
    }
    
    public function actionLogin()
    {
        if($this->getIsLogin()) {
            $this->addError('login', 1001);
            return false;
        }
        
        try {
            $auth = $this->getAccessTokenAuthentication();
        } catch (\Exception $ex) {

        }
        
        $result = $auth->login($this->username, $this->passwd);

        if(!$result) {
            $this->addError('login', 1000);
            return false;
        }
        
        return $result;
    }
    
    public function actionLogout()
    {
        $auth = $this->getAccessTokenAuthentication();
        $auth->logout();
        return [];
    }
    
    public function actionIsLogin()
    {
        return ['login_status' => $this->getIsLogin()];
    }
    
    public function getAccessTokenAuthentication()
    {
        return Yii::$container->get(AccessTokenAuthentication::class);
    }
    
    public function getIsLogin()
    {
        return !Yii::$app->user->getIsGuest();
    }

}

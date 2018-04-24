<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\components\tokenAuthentication;

use yii\base\Component as BaseComponent;
use yii\base\BootstrapInterface;
use Yii;
use admin\components\tokenAuthentication\exception\ParamNotSetException;

/**
 * Handle the Token based login. include both login and preserve status of 
 */
class AccessTokenAuthentication extends BaseComponent implements BootstrapInterface
{
    /**
     * const account status
     */
    const ACCOUNT_STATUS_NORMAL = 1;
    
    /**
     * token name
     * 
     * @var string
     */
    public $tokenName = 'token';
    
    /**
     * token timeout in minute
     * 
     * @var integer
     */
    public $tokenTimeout = 3600;
    
    /**
     * yii\web\Application
     * 
     * @var object
     */
    private $application;
    
    /**
     * DB user table accesser
     * 
     * @var object
     */
    public $dbHandler;
            
    /**
     * login with username and password
     * 
     * - when success return related taken.
     * - when failure return false.
     * 
     * @param string $username
     * @param string $password
     * @return string | bool(false)
     */
    public function login(string $username, string $password)
    {        
        $userIdentity = UserIdentity::getUserByName($username);

        if(!$userIdentity) {
            return false;
        }

        if(!Yii::$app->security->validatePassword($password, current($userIdentity::$user)->passwd)) {
            return false;
        }
        
        $token = $this->ganerateToken();
        
        current($userIdentity::$user)->updateAttributes([
            "access_token" => $token,
            "access_token_created_time" => time(),
        ]);

        Yii::$app->user->login(UserIdentity::getInstance());
        $user = current($userIdentity::$user)->toArray();
        
        return [
            'username'  => (string)$user['account'],
            'token'     => (string)$token,
        ];
    }
    
    /**
     * logout and destroy token
     * 
     * @return bool
     */
    public function logout()
    {
        $userIdentity = Yii::$app->user->getIdentity();

        if(!$userIdentity) {
            return true;
        }
        
        current($userIdentity::$user)->updateAttributes([
            "access_token" => '',
            "access_token_created_time" => 0,
        ]);
        return Yii::$app->user->logout();
    }
    
    /**
     * preserve login status when application
     * in bootstrap status.
     * 
     * @return mixed
     */
    public function bootstrap($application)
    {
        $this->configUserIdentity();
        
        $token = '';
        $this->application = $application;
        $request = $application->getRequest();

        $token = $request->get()[$this->tokenName] ?? '';
        if($token === '') {
            $token = $request->post()[$this->tokenName] ?? '';
        }
             
        if(empty($token)) {
            //ensure user is a guest;
            $application->user->logout();
            return ;
        }

        $user = $application->user->loginByAccessToken($token);
        return $this;
    }
    
    /**
     * ganerate random token value
     * 
     * @param integer $length
     * @return string 
     */
    private function ganerateToken($length = 32)
    {
        $stringRange = 'qwertyuiopasdfghjklzxcvbnmQAZWSXEDCRFVTGBYHNUJMIKOLP1234567890';        
        $randString = '';
        
        if(!is_int($length) || $length < 1)
            $length = 32;
        
        for($i=0; $i<$length; $i++) {
            $randString .= $stringRange[rand(0, strlen($stringRange)-1)];
        }
        $randString .= (string) time();
        
        
        return sha1($randString);
    }

    /**
     * Config UserIdentity class whith dn handler and timeout
     * 
     * @return void
     */
    private function configUserIdentity()
    {
        UserIdentity::$dbHandler = $this->dbHandler;
        UserIdentity::$timeout = $this->tokenTimeout;        
    }
    
    /**
     * Get current login user indentity
     * 
     * @param $onlyStoreId bool If return store id without other infomation.
     * @return mix
     */
    public static function getUser($onlyStoreId = false)
    {
        if(! $user = Yii::$app->user->getIdentity()::$user) {
            return null;
        }
        if($onlyStoreId) {
            return current($user)['store_id'];
        }
        return current($user);
    }
}

<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\components\tokenAuthentication;

use yii\base\Component as BaseComponent;
use yii\base\BootstrapInterface;
use Yii;
use commodity\components\tokenAuthentication\exception\ParamNotSetException;
use commodity\components\tokenAuthentication\exception\NotEnableUserException;

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

        if(current($userIdentity::$user)->status != 1) {
            throw new NotEnableUserException(NotEnableUserException::ERROR);
        }

        $token = $this->ganerateToken();
        $ip=Yii::$app->getRequest()->getUserIP();
        
        current($userIdentity::$user)->updateAttributes([
            "access_token" => $token,
            "access_token_created_time" => time(),
            "ip_address" => self::getCity($ip),
            "ip_number" => $ip,
            "last_login_time" => date('Y-m-d H:i:s'),
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
    
    /**
    * Gets the current login IP.
    */
    public static function getIP()
   {
       static $realip;
       if (isset($_SERVER)){
           if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
               $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
           } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
               $realip = $_SERVER["HTTP_CLIENT_IP"];
           } else {
               $realip = $_SERVER["REMOTE_ADDR"];
           }
       } else {
           if (getenv("HTTP_X_FORWARDED_FOR")){
               $realip = getenv("HTTP_X_FORWARDED_FOR");
           } else if (getenv("HTTP_CLIENT_IP")) {
               $realip = getenv("HTTP_CLIENT_IP");
           } else {
               $realip = getenv("REMOTE_ADDR");
           }
       }
       return $realip;
   }
   
   /**
    * Get the location of IP
    * 淘宝IP接口
    * @Return: string
    */
    public static function getCity($ip = '')
    {
        if($ip == ''){
            $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
            $ip=json_decode(file_get_contents($url),true);
            $data = $ip;
        }else{
            $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
            $ip=json_decode(file_get_contents($url));   
            if((string)$ip->code=='1'){
               return false;
            }
            $data = (array)$ip->data;
        }
        
        return $data['country'].$data['region'].$data['city'];   
    }
}

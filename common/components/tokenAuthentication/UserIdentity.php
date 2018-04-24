<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\tokenAuthentication;

use common\ActiveRecord\EmployeeUserAR;
use common\components\tokenAuthentication\exception;
use yii\web\IdentityInterface;
use Yii;

class UserIdentity implements IdentityInterface
{

    static public $dbHandler;
    
    static public $user;
    
    static public $timeout = 3600;


    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::getUserById($id);        
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userIdentity = self::getUserByToken($token);

        if($userIdentity) {
            self::updateTokenExpire($userIdentity::$user);
            return $userIdentity;
        }
        return false;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        if(!empty(self::$user->id)) {
            return self::$user->id;
        }
        return false;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        
    }

    static public function getUserById($id)
    {
        if(empty($id)) {
            throw new exception\InvalidArgumentException("Id cannot be null");
        }
        if(empty(self::$dbHandler)) {
            throw new exception\RuntimeException("DB handler can not found for token autnentication.");
        }
        self::$user = self::$dbHandler::find()
                ->andWhere(['id' => $id])
                ->andWhere(['status' => 1])
                ->all();
        
        if(!empty(self::$user)) {
            return self::getInstance();
        } else {
            return false;
        }
    }
    
    static public function getUserByName($username)
    {
        if(empty($username)) {
            throw new exception\InvalidArgumentException("user name cannot be null");
        }
        if(empty(self::$dbHandler)) {
            throw new Exception\RuntimeException("DB handler can not found for token autnentication.");
        }
        self::$user = self::$dbHandler::find()
                ->andWhere(['account' => $username])
                ->andWhere(['status' => 1])
                ->all();

        if(!empty(self::$user)) {
            return self::getInstance();
        } else {
            return false;
        }
    }

    static public function getUserByToken($token)
    {
        if(empty($token)) {
            throw new exception\InvalidArgumentException("token cannot be null");
        }
        self::$user = self::$dbHandler::find()
                ->andWhere(['access_token' => $token])
                ->andWhere(['>', 'access_token_created_time', time()-self::$timeout])
                ->andWhere(['status' => 1])
                ->all();

        if(!empty(self::$user)) {
            return self::getInstance();
        } else {
            return false;
        }

    }
    
    /**
     * update token ganerated time to avoid token timeout
     * 
     * @param object $user
     * @return bool
     */
    static private function updateTokenExpire($user)
    {
        current($user)->updateAttributes([
            "access_token_created_time" => time(),
        ]);
    }
    
    static public function getInstance()
    {
        return Yii::createObject([
            'class' => self::class,
        ]);
    }
}


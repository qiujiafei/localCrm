<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 13:10
 * 获取用户信息相关
 */

namespace admin\models;
use Yii;

class UserInfoLogic
{
    private static $info;

    /**
     * @return null|\yii\web\IdentityInterface
     *
     */
    public static function getUser()
    {
        return Yii::$app->user->getIdentity();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function verifyUser() {
        if(self::$info){
            return self::$info;
        }
        if (!$userIdentity = self::getUser()) {
            throw new \Exception(sprintf(
                "Can not found user identity in %s.", __METHOD__
            ));
        }

        return self::$info = $userIdentity::$user;
    }

    /**
     * @return null
     * @throws \Exception
     */
    public static function getStoreId()
    {
        null === self::$info && self::verifyUser();
        return current(self::$info)['store_id'] ?? null;
    }

    public static function getUserId()
    {
        null === self::$info && self::verifyUser();
        return current(self::$info)['id'] ?? null;
    }
}
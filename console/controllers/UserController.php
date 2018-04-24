<?php

namespace console\controllers;

use yii\console\Controller as ConsoleController;
use console\models\user\put\UserModel as PutUser;
use console\models\user\get\UserModel as GetUser;
use console\models\user\modify\UserModel as ModifyUser;
use console\models\user\delete\UserModel as DeleteUser;
use Yii;

/**
 * 用户管理
 */
class UserController extends ConsoleController
{
    protected $putUser;
    protected $getUser;
    protected $modifyUser;
    protected $deleteUser;
    
    public $username;
    public $password;
    public $mobile;
    public $name;
    public $email;
    public $store;
    
    /**
     * 创建用户
     * 
     * 参数:
     * -u username 用户名
     * -p password 密码
     * -m mobile 手机号
     * -n name 姓名
     * -e email 电子邮箱
     * -s store 店铺ID
     */
    public function actionCreate()
    {
        $putUser = Yii::createObject([
            'class'     => PutUser::class,
            'output'    => $this,
        ]);
        if(!$putUser([
            'username' => $this->username,
            'password' => $this->password,
            'mobile' => $this->mobile,
            'name' => $this->name,
            'email' => $this->email,
            'store' => $this->store
        ])) {
            return 1;
        }
        return 0;
    }

    /**
     * 修改用户信息
     * 
     * 参数:
     * -u username 用户名
     * -p password 密码
     * -m mobile 手机号
     * -n name 姓名
     * -e email 电子邮箱
     * -s store 店铺ID
     */
    public function actionModify()
    {
        $modifyUser = Yii::createObject([
            'class'     => ModifyUser::class,
            'output'    => $this,
        ]);
        if(!$modifyUser([
            'username' => $this->username,
            'password' => $this->password,
            'mobile' => $this->mobile,
            'name' => $this->name,
            'email' => $this->email,
            'store' => $this->store
        ])) {
            return 1;
        }
        return 0;
    }
    
    /**
     * 删除用户
     * 
     * 参数:
     * -u username 用户名
     */
    public function actionDelete()
    {
        $deleteUser = Yii::createObject([
            'class'     => DeleteUser::class,
            'output'    => $this,
        ]);
        if(!$deleteUser([
            'username' => $this->username,
        ])) {
            return 1;
        }
        return 0;
    }
    
    /**
     * 启用用户
     * 
     * 参数:
     * -u username 用户名
     */
    public function actionEnable()
    {
        $modifyUser = Yii::createObject([
            'class'     => ModifyUser::class,
            'output'    => $this,
        ]);
        if(!$modifyUser->enable([
            'username' => $this->username,
        ])) {
            return 1;
        }
        return 0;
    }
    
    /**
     * 停用用户
     * 
     * 参数:
     * -u username 用户名
     */
    public function actionDisable()
    {
        $modifyUser = Yii::createObject([
            'class'     => ModifyUser::class,
            'output'    => $this,
        ]);
        if(!$modifyUser->disable([
            'username' => $this->username,
        ])) {
            return 1;
        }
        return 0;
    }
    
    public function options($actionID)
    {
        $params = [
            'create' => ['username', 'password', 'mobile', 'name', 'email', 'store'],
            'delete' => ['username'],
            'disable' => ['username'],
            'enable' => ['username'],
            'modify' => ['username', 'password', 'mobile', 'name', 'email', 'store'],
        ];
        
        return $params[$actionID];
    }
    
    public function optionAliases()
    {
        return [
            'u' => 'username',
            'p' => 'password',
            'm' => 'mobile',
            'n' => 'name',
            'e' => 'email',
            's' => 'store',
        ];
    }
}

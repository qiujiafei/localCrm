<?php

namespace console\models\user\modify;

use common\models\Model as CommonModel;
use common\ActiveRecord\EmployeeUserAR;

class UserModel extends CommonModel
{
    const SCENARIO_MODIFY_USER = 'modify_user';
    const SCENARIO_ENABLE_USER = 'enable_user';
    const SCENARIO_DISABLE_USER = 'disable_user';
    
    public $output;
    
    public $username;
    public $password;
    public $mobile;
    public $name;
    public $email;
    public $store;
    
    public function rules()
    {
        return [
            [
                ['username', 'status'],
                'required',
                'message' => '缺少必要参数',
            ],
            [
                'username',
                'string',
                'length' => [6, 100],
                'tooLong' => '用户名过长',
                'tooShort' => '用户名太短',
                'message' => '用户名不为字符串',
            ],
            [
                'password',
                'string',
                'length' => [6, 100],
                'tooLong' => '密码过长',
                'tooShort' => '密码太短',
                'message' => '密码不为字符串',
            ],
            [
                'name',
                'string',
                'length' => [6, 100],
                'tooLong' => '姓名过长',
                'tooShort' => '姓名太短',
                'message' => '姓名不为字符串',
            ],
            [
                'email',
                'email',
                'message' => 'Email格式有误',
            ],
            [
                'store',
                'integer',
                'message' => 'Store ID必须为整形',
            ],
        ];
    }
    
    public function scenarios()
    {
        return [
            self::SCENARIO_MODIFY_USER => ['username', 'password', 'mobile', 'name', 'email', 'store'],
            self::SCENARIO_ENABLE_USER => ['username', 'status'],
            self::SCENARIO_DISABLE_USER => ['username', 'status'],
        ];
    }
    
    public function __invoke(array $attrs)
    {
        $this->scenario = self::SCENARIO_MODIFY_USER;
        $this->attributes = $attrs;
        
        if(!$this->validate()) {
            $this->output->stderr($this->errorArrayToString($this->getErrors()));
            return false;
        }
        if(!$this->isExistUsername($this->username)) {
            $this->output->stderr(sprintf("用户名%s不存在\n", $this->username));
            return false;
        }
        if($this->isExistName($this->name)) {
            $this->output->stderr(sprintf("用户姓名%s已存在\n", $this->name));
            return false;
        }
        if($this->isExistEmail($this->email)) {
            $this->output->stderr(sprintf("用户邮箱%s已存在\n", $this->email));
            return false;
        }
        if(!$this->isValidStore($this->store)) {
            $this->output->stderr(sprintf("店铺%s不存在\n", $this->store));
            return false;
        }
        
        if(!$this->modifyUser()) {
            $this->output->stderr("用户修改失败\n");
            return false;
        }
        
        $this->output->stdout("用户修改成功\n");
        return true;
    }

    public function enable($attrs)
    {
        $this->scenario = self::SCENARIO_ENABLE_USER;
        $this->attributes = $attrs;
        if(!$user = EmployeeUserAR::findOne(['account' => $this->username])) {
            $this->output->stderr(sprintf("用户%s不存在\n", $this->username));
            return false;
        }
        $user->status = 1;
        $user->last_modified_time = date('Y-m-d H:i:s');
        if($user->save()) {
            $this->output->stdout("用户启用成功\n");
            return true;
        }
        $this->output->stderr("用户启用失败\n");
        return false;
    }
    
    public function disable($attrs)
    {
        $this->scenario = self::SCENARIO_DISABLE_USER;
        $this->attributes = $attrs;
        if(!$user = EmployeeUserAR::findOne(['account' => $this->username])) {
            $this->output->stderr(sprintf("用户%s不存在\n", $this->username));
            return false;
        }
        $user->access_token = '';
        $user->access_token_created_time = '';
        $user->status = 0;
        $user->last_modified_time = date('Y-m-d H:i:s');
        if($user->save()) {
            $this->output->stdout("用户停用成功\n");
            return true;
        }
        $this->output->stderr("用户停用失败\n");
        return false;
    }
    
    private function isExistUsername($username)
    {
        return EmployeeUserAR::find()->where(['account' => $username])->exists();
    }
    
    private function isExistName($name)
    {
        $user = EmployeeUserAR::find()
                ->where(['account' => $this->username])
                ->orWhere(['name' => $name])
                ->all();
        return count($user) > 1;
    }
    
    private function isExistEmail($email)
    {
        $user = EmployeeUserAR::find()
                ->where(['account' => $this->username])
                ->orWhere(['email' => $email])
                ->all();
        return count($user) > 1;
    }
    
//目前没有商店管理    
    private function isValidStore($id)
    {
        return true;
    }
    
    private function modifyUser()
    {
        $date = date('Y-m-d H:i:s');
        $user = EmployeeUserAR::findOne(['account' => $this->username]);
        if($this->password) {
            $user->passwd = password_hash($this->password, PASSWORD_DEFAULT);
            $user->last_modified_time = $date;
        }
        if($this->mobile) {
            $user->mobile = $this->mobile;
            $user->last_modified_time = $date;
        }
        if($this->name) {
            $user->name = $this->name;
            $user->last_modified_time = $date;
        }
        if($this->email) {
            $user->email = $this->email;
            $user->last_modified_time = $date;
        }
        if($this->store) {
            $user->store_id = $this->store;
            $user->last_modified_time = $date;
        }
        if($user->getOldAttribute('last_modified_time') != $user->getAttribute('last_modified_time')) {
            return $user->save();
        } else {
            return false;
        }
    }
    
    public function errorArrayToString(array $errors)
    {
        $errorString = '';
        foreach($errors as $key => $error) {
            $errorString .= sprintf("参数%s错误:%s\n", $key, current($error));
        }
        return $errorString;
    }    
}

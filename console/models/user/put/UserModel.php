<?php

namespace console\models\user\put;

use common\models\Model as CommonModel;
use common\ActiveRecord\EmployeeUserAR;

class UserModel extends CommonModel
{
    const SCENARIO_PUT_USER = 'put_user';
    
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
                ['username', 'password', 'name', 'store'],
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
            'put_user' => ['username', 'password', 'mobile', 'name', 'email', 'store'],
        ];
    }
    
    public function __invoke(array $attrs)
    {
        $this->scenario = self::SCENARIO_PUT_USER;
        $this->attributes = $attrs;
        
        if(!$this->validate()) {
            $this->output->stderr($this->errorArrayToString($this->getErrors()));
            return false;
        }
        if($this->isExistUsername($this->username)) {
            $this->output->stderr(sprintf("用户名%s已存在\n", $this->username));
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
        
        if(!$this->insertUser()) {
            $this->output->stderr("用户创建失败\n");
            return false;
        }
        
        $this->output->stdout("用户创建成功\n");
        return true;
    }

    private function isExistUsername($username)
    {
        return EmployeeUserAR::find()->where(['account' => $username])->exists();
    }
    
    private function isExistName($name)
    {
        return EmployeeUserAR::find()->where(['name' => $name])->exists();
    }
    
    private function isExistEmail($email)
    {
        return EmployeeUserAR::find()->where(['email' => $email])->exists();
    }
    
//目前没有商店管理    
    private function isValidStore($id)
    {
        return true;
    }
    
    private function insertUser()
    {
        $date = date('Y-m-d H:i:s');
        $user = new EmployeeUserAR();
        $user->account      = $this->username;
        $user->passwd       = password_hash($this->password, PASSWORD_DEFAULT);
        $user->mobile       = $this->mobile ?? '';
        $user->name         = $this->name;
        $user->email        = $this->email ?? '';
        $user->store_id     = $this->store;
        $user->status       = 1;
        $user->created_by   = -1;
        $user->last_modified_time = $date;
        $user->created_time = $date;
        return $user->save();
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

<?php

namespace console\models\user\delete;

use common\models\Model as CommonModel;
use common\ActiveRecord\EmployeeUserAR;

class UserModel extends CommonModel
{
    const SCENARIO_DELETE_USER = 'delete_user';
    
    public $output;
    
    public $username;

    public function rules()
    {
        return [
            [
                ['username'],
                'required',
                'message' => '缺少必要参数',
            ],
        ];
    }
    
    public function scenarios()
    {
        return [
            'delete_user' => ['username'],
        ];
    }
    
    public function __invoke(array $attrs)
    {
        $this->scenario = self::SCENARIO_DELETE_USER;
        $this->attributes = $attrs;
        
        if(!$this->validate()) {
            $this->output->stderr($this->errorArrayToString($this->getErrors()));
            return false;
        }
        if(!$this->isExistUsername($this->username)) {
            $this->output->stderr(sprintf("用户名%s不存在\n", $this->username));
            return false;
        }
        if(!$this->deleteUser()) {
            $this->output->stderr("删除用户失败\n");
            return false;
        }
        
        $this->output->stdout("删除用户成功\n");
        return true;
    }
    
    private function deleteUser()
    {
        $user = EmployeeUserAR::findOne(['account' => $this->username]);
        return $user->delete();
    }

    private function isExistUsername($username)
    {
        return EmployeeUserAR::find()->where(['account' => $username])->exists();
    }
}

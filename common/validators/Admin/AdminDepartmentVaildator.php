<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/17
 * Time: 11:02
 */

namespace common\validators\admin;


use common\ActiveRecord\AdminDepartmentAR;
use common\models\Validator;

class AdminDepartmentVaildator extends Validator
{
    public $message;//已存在
    public $id;//部门ID
    public $name;

    public function validateValue($value)
    {

        if (!empty($this->name)) {
            return $this->checkName($this->name);
        } elseif ($this->id) {
            return $this->checkId($this->id);
        }
    }


    //检测部门ID是否存在，存在，返回true ,不存在，返回代码
    public function checkId($value)
    {
        return AdminDepartmentAR::find()->where(['id' => $value])->exists() ? true : $this->message;
    }

    //检测部门名称是否被使用，
    public function checkName($value)
    {
        //当id 大于0时，表示验证编辑数据
        $where = [];
        if ($this->id > 0) {
            $where["id"] = "<>" . $this->id;
        }
        $where["name"] = $value;
        return AdminDepartmentAR::find()->where($where)->exists() ? $this->message : true;
    }

}
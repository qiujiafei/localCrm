<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/17
 * Time: 11:31
 */

namespace common\validators\admin;


use common\ActiveRecord\AdminPermissionAR;
use common\models\Validator;

class AdminPermissionVaildator extends  Validator
{

    public $id;
    public $message;

    public function validateValue($value)
    {
        if(is_numeric($value)){
            return $this->checkId($value);
        }else{
            return $this->checkName($value);
        }
    }


    //检测ID
    public function checkId($value){
        return AdminPermissionAR::find()->where(['id'=>$value])->exists()?true:$this->message;
    }


    //检测名称
    public function checkName($value){
        $where=[];
        if($this->id>0){
            $where['id']="<>".$this->id;
        }
        $where["name"]=$value;
        return AdminPermissionAR::find()->where($where)->exists()?$this->message:true;
    }

}
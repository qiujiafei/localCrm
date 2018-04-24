<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/17
 * Time: 11:21
 */

namespace common\validators\admin;


use admin\modules\site\models\AdminroleModel;
use common\ActiveRecord\AdminRoleAR;
use common\ActiveRecord\AdminRolePermissionAR;
use common\models\Validator;

class AdminRoleVaildator extends Validator
{
    public $message;
    public $id;
    public $permissionId;
    public $rolePermissionExists;//角色权限配置已存在时，返回代码
    public $rolePermissionNotExists;

    public $scenarios;

    public function validateValue($value)
    {

        if (is_numeric($value)) {
            return $this->checkId($value);
        } else {
            //非数值时，检测是否重名
            return $this->checkName($value);
        }
    }


    //检测ID是否存在
    private function checkId($value)
    {
        //角色是否存在
        $roleExists = AdminRoleAR::find()->where(['id' => $value])->exists();
        if ($roleExists) {
            if ($this->scenarios == AdminroleModel::SCE_BIND_PERMISSION || $this->scenarios == AdminroleModel::SCE_REVOKE_PERMISSION) {
                //获取角色配置权限是否存在
                $rolePermissionExists = AdminRolePermissionAR::find()->where(['admin_role_id' => $value, 'admin_permission_id' => $this->permissionId])->exists();
                if ($this->scenarios == AdminroleModel::SCE_BIND_PERMISSION) {
                    //绑定时，如果存在，则提示错误，否则，返回true
                    return $rolePermissionExists ? $this->rolePermissionExists : true;
                } else {
                    //解绑时，如果存在，则返回true 反之，返回错误编码
                    return $rolePermissionExists ? true : $this->rolePermissionNotExists;
                }
            }

            return true;
        } else {
            return $this->message;
        }
    }

    //检测名称是否已被使用
    private function checkName($value)
    {
        $where = [];
        if ($this->id > 0) {
            $where['id'] = "<>" . $this->id;
        }
        $where['role_name'] = $value;
        return AdminRoleAR::find()->where($where)->exists() ? $this->message : true;
    }

}
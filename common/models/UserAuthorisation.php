<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\models;

use common\ActiveRecord\UserPermissionAR;
use common\ActiveRecord\UserRolePermissionAR;
use common\ActiveRecord\UserPermissionRolesAR;
use Yii;

class UserAuthorisation
{
    //判断当前用户在当前方法下是否存在权限
    public function isExistsPermission($module, $controllerName, $actionName)
    {
        if(!$id = $this->getUserId()) {
            throw new \Exception(sprintf(
                "Id should not be null at %s.". __METHOD__
            ));
        }
        
        $where = "`" . UserPermissionRolesAR::getTableSchema()->fullName . "`.`user_id`={$id}";
        $where .= " and module_name='$module' and controller_name='$controllerName' and action_name='$actionName'";
        return UserPermissionAR::find()
            ->leftJoin('`' . UserRolePermissionAR::getTableSchema()->fullName . "` on `" . UserRolePermissionAR::getTableSchema()->fullName . "`.`permission_id`=`" . UserPermissionAR::getTableSchema()->fullName . "`.`id`")
            ->leftJoin("`" . UserPermissionRolesAR::getTableSchema()->fullName . "` on `" . UserPermissionRolesAR::getTableSchema()->fullName . "`.`role_id`=`" . UserRolePermissionAR::getTableSchema()->fullName . "`.`role_id`")
            ->where($where)->exists();
    }
    
    public function getUserId()
    {
        if($userIdentity = Yii::$app->user->getIdentity()::$user) {
            return current($userIdentity)->id;
        }
        return null;
    }
}

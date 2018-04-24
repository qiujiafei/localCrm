<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\Authorization\models;

use common\models\Model as CommonModel;
use common\components\tokenAuthentication\AccessTokenAuthentication as User;
use common\components\rbac\roles\Admin;
use Yii;

class MenuModel extends CommonModel
{
    const GET_ALL_MENU = 'get_all_menu';

    public $rbac;

    public function rules()
    {
        return [
            [
                ['username', 'passwd', 'token'],
                'required',
                'message' => 2004
            ],
        ];
    }
    
    public function scenarios()
    {
        return [
            self::GET_ALL_MENU => ['rbac'],
        ];
    }
    
    public function getAllMenu()
    {
        $roles = $this->rbac->getRoleNameByUserId(User::getUser()->id);
        $type = 0;

        foreach($roles as $role) {
            if($role instanceof Admin) {
                $type = 1;
            }
        }

        if($type == 1) {
            return ['type' => $type, 'menu' => $this->rbac->getAllMenus()];
        } else {
            return ['type' => $type, 'menu' => $this->rbac->getMenus()];
        }
    }
}

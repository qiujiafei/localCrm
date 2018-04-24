<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\Authorization\models;

use common\models\Model as CommonModel;
use common\components\tokenAuthentication\AccessTokenAuthentication as User;
use Yii;

class ResourceModel extends CommonModel
{
    const GET_ALL_RESOURCE = 'get_all_resource';
    const MODIFY = 'modify';

    public $rbac;
    public $fobiden;
    public $allow;
    public $user_id;

    public function rules()
    {
        return [
            [
                ['user_id', 'rbac'],
                'required',
                'message' => 2004
            ],
            [
                'user_id',
                'integer',
                'message' => 3006
            ],
        ];
    }
    
    public function scenarios()
    {
        return [
            self::GET_ALL_RESOURCE => ['user_id', 'rbac'],
            self::MODIFY => ['user_id', 'fobiden', 'allow', 'rbac'],
        ];
    }
    
    public function getAllResource()
    {
        $dbHandler = $this->rbac->getDbHandler();
        $resource = $dbHandler->getAllResource();

        $allResource = [];

        foreach($resource as $res) {
            $menu = $dbHandler->getMenu($res['menu_id']);
            $menu = (isset($menu['name']) ? $menu['name'] : '其他');
            $allResource[$menu][] = [
                'id' => $res['id'],
                'name' => $res['name'],
                'is_valid' => $this->rbac->exists([
                    'userId'        => $this->user_id,
                    'module'        => $res['module_name'],
                    'controller'    => $res['controller_name'],
                    'action'        => $res['action_name'],
                ]),
            ];
        }
        return $allResource; 
    }

    public function modify()
    {
        $fobiden = 0;
        $allow = 0;
        if(! empty($this->allow)) {
            $allow = $this->rbac->allow($this->user_id, $this->allow);
        }
        if(! empty($this->fobiden)) {
            $fobiden = $this->rbac->fobiden($this->user_id, $this->fobiden);
        }
        return [
            'updated' => $allow + $fobiden,
        ];
    }
}

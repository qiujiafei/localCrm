<?php

/* *
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace admin\modules\store\models\delete;

use common\models\Model as BaseModel;
use admin\modules\store\models\delete\db\Del;

class DeleteModel extends BaseModel
{
    const ACTION_DEL = 'action_del';
    
    public $id;
    
    public function scenarios()
    {
        return [
            self::ACTION_ONE => ['id'],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['id'],
                'required',
                'message' => 1,
            ],
        ];
    }
    
    public function actionDel()
    {
        try {
            if (Del::delAdminUser(['id'=>$this->id])) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 1014) {
                $this->addError('del', 1014);
                return false;
            }  else {
                $this->addError('del', 1015);
                return false;
            }
        }
    }
}

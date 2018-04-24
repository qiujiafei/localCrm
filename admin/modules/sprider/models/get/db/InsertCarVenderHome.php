<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\sprider\models\get\db;

use common\ActiveRecord\CarVenderHomeAR;
use Yii;

class InsertCarVenderHome extends CarVenderHomeAR
{

    //添加
    public static function insertData(array $data) {

        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }
            
            if($Insert->save(false)===false){
                return false;
            }
            
            return $Insert->id;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
}

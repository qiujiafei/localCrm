<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\modify\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityImageAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use commodity\modules\commodityManage\models\judgment\HasSameCommodity as Judgment;
use Yii;

class Update extends CommodityAR
{
    public static function modify($origin_name, $origin_barcode, $params)
    {
        $params = self::prepareData($params);

        if($params['commodity_name'] != $origin_name || $params['barcode'] != $origin_barcode) {
            if((new Judgment($params))()) {
                throw new \Exception("", 23000);
            }
        }

        if(!empty($params['images'])) {
            $images = $params['images'];
        }
        
        unset($params['images']);
//must add images update function.        
        $commodity = self::findOne([
            'commodity_name' => $origin_name,
            'barcode' => $origin_barcode,
            'store_id' => AccessTokenAuthentication::getUser(true)
        ]);

        if($commodity) {
            $commodity->attributes = $params;               
            $commodity->updateAttributes($params, false);
            $result = $commodity->save();
        } else {
            throw new \Exception("FOUND_NO_RESULT");
        }
        return $result;
    }
    
    public static function prepareData(array $data)
    {
        //判断user是否存在
        if(!$userIdentity = AccessTokenAuthentication::getUser()) {
            throw new \Exception(sprintf(
                "Can not found user identity in %s.", __METHOD__
            ));
        }

        //添加固定参数
        $data['last_modified_by']    = current($userIdentity)['id'];
        $data['last_modified_time']  = date('Y-m-d H:i:s');
        $data['price']               = round($data['price'], 2);

        return $data;
    }
}

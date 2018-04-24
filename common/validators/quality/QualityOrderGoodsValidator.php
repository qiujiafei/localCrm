<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 11:10
 */

namespace common\validators\quality;


use common\ActiveRecord\BusinessAreaTechnicanAR;
use common\ActiveRecord\QualityPackageAR;
use common\ActiveRecord\QualityPlaceAR;
use common\models\Validator;

class QualityOrderGoodsValidator extends  Validator
{

    public $message;
    public $messageRoundNum;
    public $messageSales;

    public function validateValue($goods){
        foreach($goods as $key=>$var){
            //验证字段是否存在
            if(!array_key_exists("package_id",$var)
                ||!array_key_exists('place_id',$var)
                ||!array_key_exists('sales',$var) || !array_key_exists('round_num',$var) || !array_key_exists('technician',$var))
            {
                return $this->message;
            }

            if(empty($var['sales'])){
                return $this->messageSales;
            }
            if(empty($var['round_num']) || strlen($var['round_num']) < 1 || strlen($var['round_num']) >25){
                return $this->messageRoundNum;
            }

            //验证取值是否正确
            if(!QualityPackageAR::find()->where(['id'=>$var['package_id']])->exists()
                ||!QualityPlaceAR::find()->where(['id'=>$var['place_id']])->exists()
                ||!BusinessAreaTechnicanAR::find()->where(['id'=>$var['technician']])->exists()
            ){
                return $this->message;
            }
        }
        return true;
    }

}
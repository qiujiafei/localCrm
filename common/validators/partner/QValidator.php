<?php
namespace common\validators\partner;

use common\models\Validator;
use common\models\parts\partner\UrlParamCrypt;
use common\models\parts\partner\PartnerPromoter;

class QValidator extends Validator{

    public $message;
    public $unavailable;

    protected function validateValue($q){
        if($id = (new UrlParamCrypt)->decrypt($q)){
            try{
                $promoter = new PartnerPromoter(['id' => $id]);
                if($promoter->isAvailable){
                    return true;
                }else{
                    return $this->unavailable;
                }
            }catch(\Exception $e){
                return $this->message;
            }
        }else{
            return $this->message;
        }
    }
}

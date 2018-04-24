<?php
namespace common\validators\partner;

use common\models\Validator;
use common\models\parts\partner\UrlParamCrypt;
use common\models\parts\partner\PartnerApply;

class AValidator extends Validator{

    public $message;

    protected function validateValue($a){
        if($id = (new UrlParamCrypt)->decrypt($a)){
            try{
                new PartnerApply(['id' => $id]);
                return true;
            }catch(\Exception $e){
                return $this->message;
            }
        }else{
            return $this->message;
        }
    }
}

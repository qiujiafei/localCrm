<?php
namespace common\validators\BusinessArea;

use common\models\Validator;
use common\ActiveRecord\BusinessQuaternaryAreaAR;

class QuaternaryAreaValidator extends Validator{

    public $message;
    public $topArea;
    public $secondaryArea;
    public $tertiaryArea;

    protected function validateValue($id){
        $areaAR = BusinessQuaternaryAreaAR::findOne($id);
        if(!$areaAR)return $this->message;
        if(!is_null($this->topArea) &&
            !is_null($this->secondaryArea) &&
            !is_null($this->tertiaryArea)
        ){
            if($areaAR->business_top_area_id != $this->topArea ||
                $areaAR->business_secondary_area_id != $this->secondaryArea ||
                $areaAR->business_tertiary_area_id != $this->tertiaryArea
            ){
                return $this->message;
            }
        }
        return true;
    }
}

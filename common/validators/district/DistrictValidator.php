<?php
namespace common\validators\district;

use common\models\Validator;
use common\ActiveRecord\DistrictProvinceAR;
use common\ActiveRecord\DistrictCityAR;
use common\ActiveRecord\DistrictDistrictAR;

class DistrictValidator extends Validator{

    public $message;
    /**
     * 如果区ID = 0
     * 根据指定的省、市ID验证该市下是否无区级
     */
    public $province;
    public $city;

    protected function validateValue($districtId){
        //省、市为空；仅验证区，且区必须存在
        if(empty($this->province) && empty($this->city)){
            if(!DistrictDistrictAR::findOne($districtId))return $this->message;
        }
        if($districtId){//区-明确ID
            if($district = DistrictDistrictAR::findOne($districtId)){//区ID存在验证
                if(!is_null($this->province)){//省ID验证
                    if($district->district_province_id != $this->province)return $this->message;
                }
                if(!is_null($this->city)){//市ID验证
                    if($district->district_city_id != $this->city)return $this->message;
                }
                return true;
            }else{
                return $this->message;
            }
        }else{//区-未知ID
            if($province = DistrictProvinceAR::findOne($this->province)){//省ID验证，区未知则省必须存在
                if($city = DistrictCityAR::findOne($this->city)){//市ID验证
                    if($city->district_province_id == $province->id){
                        if(DistrictDistrictAR::findOne(['district_city_id' => $city->id])){//验证区是否存在
                            return $this->message;
                        }else{
                            return true;
                        }
                    }else{
                        return $this->message;
                    }
                }else{
                    if(DistrictCityAR::findOne(['district_province_id' => $province->id])){//验证市是否存在
                        return $this->message;
                    }else{
                        if(DistrictDistrictAR::findOne(['district_province_id' => $province->id])){//验证区是否存在
                            return $this->message;
                        }else{
                            return true;
                        }
                    }
                }
            }else{
                return $this->message;
            }
        }
    }
}

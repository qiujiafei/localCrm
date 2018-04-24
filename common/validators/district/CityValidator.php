<?php
namespace common\validators\district;

use common\models\Validator;
use common\models\parts\district\Province;
use common\models\parts\district\City;

class CityValidator extends Validator{

    public $message;
    /**
     * 如果市ID = 0
     * 根据指定的province验证是否该省无市级
     */
    public $province;

    protected function validateValue($cityId){
        try{
            $city = new City([
                'provinceId' => $this->province,
                'cityId' => $cityId,
            ]);
        }catch(\Exception $e){
            if($cityId == 0){
                try{
                    $province = new Province([
                        'provinceId' => $this->province,
                    ]);
                }catch(\Exception $e){
                    return $this->message;
                }
                if(!$province->hasChild)return true;
            }
            return $this->message;
        }
        return $city->validate() ? true : $this->message;
    }
}

<?php

namespace common\validators\admin;


use common\ActiveRecord\BrandHomeAR;
use common\models\Validator;
use Yii;

class AdminHotBrandVaildator extends Validator
{
    public $message;
    public $sortMessage;
    public $urlMessage;

    public function validateValue($value)
    {
        foreach ($value as $v)
        {
            if (!$this->checkId($v['id'])){
                return $this->message;
            }elseif ($this->checkSort($v['id'], $v['hot_sort'])){
                return $this->sortMessage;
            }elseif ( empty($this->checkUrl($v['url']))){
                return $this->urlMessage;
            }
        }
        return true;
    }

    private function checkId($id)
    {
        return Yii::$app->RQ->AR(new BrandHomeAR())->exists([
            'where' => [
                'type' => BrandHomeAR::TYPE_HOT_BRAND,
                'status' => [BrandHomeAR::STATUS_AVAILABLE,BrandHomeAR::STATUS_UNAVAILABLE],
                'id' => $id,
            ],
            'limit' => 1,
        ]);
    }

    private function checkSort($id, $sort)
    {
        $where = ['and','type='.BrandHomeAR::TYPE_HOT_BRAND,'status='.BrandHomeAR::STATUS_AVAILABLE ,'sort='.$sort,'id <>'.$id];
        return Yii::$app->RQ->AR(new BrandHomeAR())->exists([
            'where' => $where,
            'limit' => 1,
        ]);
    }

    private function checkUrl($url){
        $rule = '/^((http|ftp|https):\/\/)+[\w-_.]+(\/[\w-_]+)*\/?$/';
        return preg_match($rule,$url,$result);

    }


}
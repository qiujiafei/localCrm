<?php

namespace common\validators\admin;


use common\ActiveRecord\BrandHomeAR;
use common\models\Validator;
use Yii;

class AdminBrandAlbumVaildator extends Validator
{
    public $message;

    public function validateValue($value)
    {
        if (is_array($value))
        {
            foreach ($value as $id)
            {
                if (!$this->checkId($id))
                {
                    return $this->message;
                }
            }
        }
        else
        {
            if (!$this->checkId($value))
            {
                return $this->message;
            }
        }
        return true;
    }

    private function checkId($id)
    {
        return Yii::$app->RQ->AR(new BrandHomeAR())->exists([
            'where' => [
                'status' => [BrandHomeAR::STATUS_AVAILABLE,BrandHomeAR::STATUS_UNAVAILABLE],
                'id' => $id,
            ],
            'limit' => 1,
        ]);
    }


}
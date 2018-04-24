<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/27
 * Time: 10:30
 */

namespace common\models;


use yii\base\Exception;
use yii\db\ActiveRecordInterface;

/**
 * Class Object
 * @package common\models
 * @property $AR ActiveRecord
 */
class Object extends \yii\base\Object
{
    /**
     * @var Model
     */
    protected  $AR;

    public function __get($name)
    {
        try {
            return $this->AR->$name;
        }catch (Exception $e){
            return parent::__get($name);
        }
    }

    /**
     * @param ActiveRecordInterface $ar
     */
    public function setAr($ar)
    {
        $this->AR = $ar;
    }

    public function getAr()
    {
        return $this->AR;
    }

    public function getAttributes($names = null)
    {
        return $this->AR->getAttributes($names);
    }

}
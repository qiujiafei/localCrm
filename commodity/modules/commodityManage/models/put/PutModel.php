<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\put;

use common\models\Model as CommonModel;
use commodity\modules\commodityManage\models\put\db\Insert;
use commodity\modules\commodityManage\models\get\db\GetIsClassification;
use commodity\modules\commodityManage\models\get\db\GetIsProperty;
use commodity\models\interfaces\DepotInterface;
use commodity\models\build\DepotBuildModel;
use common\exceptions;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';
    const DEFAULT_COMMODITY_PROPERTY = '其他';
   
    public $commodity_name;//商品名 string
    public $specification;//商品规格 string
    public $classification_name;//分类 IDinteger
    public $price;//价格 decimal两位小数
    public $barcode;//条码 string
    public $unit_name;//单位ID integer
    public $property;//商品属性ID integer
    public $commodity_code;//商品ID integer
    public $default_depot_id;//默认仓库
    public $comment;//商品备注 string
    public $status;//商品状态 integer
    public $images;//商品图片名称数组 string
    public $commodity_property_name;
    
    public function scenarios()
    {
        return [
            self::ACTION_INSERT => [
                'commodity_name', 'specification',
                'classification_name', 'price','barcode',
                'unit_name','property',
                'commodity_code','default_depot_id','comment','status','images','commodity_property_name'
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['commodity_name',
                'classification_name', 'price','barcode',
                'unit_name'],
                'required',
                'message' => 2004,
            ],
            [
                ['commodity_name', 'default_depot_id'],
                'string',
                'length' => [0, 30],
                'tooLong' => 2007,
            ],
            [
                ['commodity_code'],
                'string',
                'length' => [0, 30],
                'tooLong' => 2008,
            ],
            [
                ['specification'],
                'string',
                'length' => [0, 30],
                'tooLong' => 2009,
            ],
            [
                ['price'],
                'double',
                'min' => 0,
                'max' => 9999999999,
                'tooSmall' => 2010,
                'tooBig' => 2010,
            ],
        ];
    }
    
    public function actionInsert()
    {
        if(! $insertArray = $this->ensureParams()) {
            return false;
        }

        try {
            Insert::insertCommodity($insertArray);
        } catch(\Exception $ex) {
            if($ex->getCode() === 23000) {
                $this->addError('action insert', 2001);
                return false;
            } elseif($ex->getCode() === 2003) {
                $this->addError('action insert', 2003);
                return false;
            } else {
                $this->addError('action insert', 2000);
                return false;
            }
        }
        
        return [];
    }
    
    public function ensureParams()
    {
        $result = [];
        
        if(! ($depot = (new DepotBuildModel)) instanceof DepotInterface) {
            throw new exceptions\UnknownObjectImplementException();
        }
        
        if(! $classification_name = $this->getIsClassification($this->classification_name)) {
            return false;
        }
        $property = $this->getIsProperty($this->commodity_property_name);
        
        $result['commodity_name']       = $this->commodity_name;
        $result['specification']        = $this->specification;
        $result['classification_name']  = $classification_name;
        $result['price']                = $this->price;
        $result['barcode']              = $this->barcode;
        $result['unit_name']            = $this->unit_name;
        $result['commodity_property_name']= $property;
        $result['commodity_code']       = $this->commodity_code;
        $result['default_depot_id']   = $depot->isValidDepotId($this->default_depot_id) ? $this->default_depot_id : '';
        $result['comment']              = $this->comment;
        $result['status']               = $this->status;
        $result['images']               = $this->images;

        return $result;
    }
    
    private function getIsProperty($name)
    {
        $property = (new GetIsProperty)($name) ? $name : static::DEFAULT_COMMODITY_PROPERTY;
        return $property;
    }
    
    private function getIsClassification($name)
    {
        if(! $is = (new GetIsClassification())($name)) {
            $this->addError('getIsClassification', 2013);
            return false;
        }
        return $name;
    }
}


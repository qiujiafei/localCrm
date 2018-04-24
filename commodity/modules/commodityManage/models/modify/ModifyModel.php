<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\modify;

use common\models\Model;
use commodity\modules\commodityManage\models\modify\db\Update;
use commodity\modules\commodityManage\models\get\db\GetPropertyId;
use commodity\modules\commodityManage\models\get\db\GetIsClassification;
use commodity\modules\commodityManage\models\get\db\GetClassificationIdByName;
use commodity\modules\commodityManage\models\get\db\GetIsProperty;
use commodity\models\interfaces\DepotInterface;
use commodity\models\build\DepotBuildModel;
use common\exceptions;

class ModifyModel extends Model
{
    const ACTION_UPDATE = 'action_update';
    const DEFAULT_COMMODITY_PROPERTY = '其他';
    
    public $origin_name;
    public $origin_barcode;
    
    public $commodity_name;//商品名 string
    public $specification;//商品规格 string
    public $classification_name;//分类 IDinteger
    public $price;//价格 decimal两位小数
    public $barcode;//条码 string
    public $unit_name;//单位ID integer
    public $property;//商品属性ID integer
    public $originate_id;//来源ID integer
    public $commodity_code;//商品ID integer
    public $default_depot_id;//默认仓库
    public $comment;//商品备注 string
    public $status;//商品状态 integer
    public $images;//商品图片名称数组 [string...]
    public $commodity_property_name;//商品图片名称数组 [string...]
    
    public function scenarios()
    {
        return [
            self::ACTION_UPDATE => [
                'origin_name', 'origin_barcode',
                'commodity_name', 'specification',
                'classification_name', 'price','barcode',
                'unit_name','property','originate_id',
                'commodity_code','default_depot_id','comment','status','images','commodity_property_name'
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['commodity_name','origin_name','origin_barcode',
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
    
    public function actionUpdate()
    {
        if(!$params = $this->ensureParams()) {
            return false;
        }
        try {
            $result = Update::modify($this->origin_name, $this->origin_barcode, $params);
        } catch(\Exception $ex) {
            if($ex->getCode() === 23000) {
                $this->addError('action insert', 2001);
                return false;
            } elseif($ex->getCode() === 2003) {
                $this->addError('action insert', 2003);
                return false;
            } elseif($ex->getMessage() === 'FOUND_NO_RESULT') {
                $this->addError('action insert', 2011);
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
        
        if(isset($this->default_depot_id)) {
//            if(! ($depot = (new DepotBuildModel)) instanceof DepotInterface) {
//                throw new exceptions\UnknownObjectImplementException();
//            }
//            if($depot->isValidDepot($this->default_depot_id)) {
                $result['default_depot_id'] = $this->default_depot_id;
//            } else {
//                $this->addError('ensureParams', 2014);
//                return false;
//            }
        }
        
        if(isset($this->classification_name)) {
            try {
                $result['classification_id'] = (new GetClassificationIdByName)($this->classification_name);
            } catch(\Exception $ex) {
                if($ex->getMessage() == GetClassificationIdByName::ERROR_CLASSIFICATION_NOT_FOUND) {
                    $this->addError('ensureParams', 2013);
                    return false;
                }
                $this->addError('ensureParams', -1);
                return false;
            }
            $result['classification_name']  = $this->classification_name;
        }
        $this->property=$this->commodity_property_name;
        if(isset($this->property)) {
            $result['commodity_property_name'] = $this->getIsProperty($this->property);
        }
        
        if(isset($this->commodity_name)) {
            $result['commodity_name'] = $this->commodity_name;
        }
        if(isset($this->specification)) {
            $result['specification'] = $this->specification;
        }
        if(isset($this->price)) {
            $result['price'] = $this->price;
        }
        if(isset($this->barcode)) {
            $result['barcode'] = $this->barcode;
        }
        if(isset($this->unit_name)) {
            $result['unit_name'] = $this->unit_name;
        }
        if(isset($this->commodity_code)) {
            $result['commodity_code'] = $this->commodity_code;
        }
        if(isset($this->comment)) {
            $result['comment'] = $this->comment;
        }
        if(isset($this->status)) {
            $result['status'] = $this->status;
        }
        if(isset($this->images)) {
            $result['images'] = $this->images;
        }

        return $result;
    }
    
    private function getIsProperty($name)
    {
        $property = (new GetIsProperty)($name) ? $name: static::DEFAULT_COMMODITY_PROPERTY;
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

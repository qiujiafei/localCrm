<?php
namespace common\validators\product;

use common\models\Validator;
use common\models\RapidQuery;
use common\ActiveRecord\ProductAR;
use yii\base\InvalidConfigException;

class SaleStatusValidator extends Validator{
    
    //商品id 必须设置
    public $productId;
    /**
     * 商品原本的销售状态，商品当前销售状态满足$this->originStauts才允许修改，默认不检测原本的销售状态
     * 可接受参数类型：array | string 
     */
    public $originStatus = false;
    /**
     * 可以被设置的销售状态，被验证值必须满足$this->canModifyTo才允许修改，默认不允许修改销售状态
     * 可接受参数类型：array | string
     */
    public $canModifyTo = false;
    //错误码
    public $message;

    //商品表ActiveRecord对象
    private $_productAR;

    public function init(){
        parent::init();
    }

    /**
     * 验证销售状态
     * 当设置$this->originStatus时，商品ID为$this->productId的商品的销售状态必须通过$this->originStatus验证
     * 当$this->canModifyTo未设置时，任何状态修改都不被通过
     */
    protected function validateValue($saleStatus){
        try{
            if(is_null($this->productId) || !$this->_productAR = ProductAR::findOne($this->productId))throw new InvalidConfigException;
            if($this->originStatus){
                if(!$this->isMeetCondition($this->_productAR->sale_status, $this->originStatus))throw new \Exception;
            }
            if($this->canModifyTo){
                if(!$this->isMeetCondition($saleStatus, $this->canModifyTo))throw new \Exception;
            }else{
                throw new \Exception;
            }
            return true;
        }catch(\Exception $e){
            return $this->message;
        }
    }

    /**
     * 是否符合条件
     *
     * @param string $param 需要被检测的参数
     * @param array|string $condition 正确参数集合
     *
     * @return boolean
     */
    protected function isMeetCondition($param, $condition){
        if(is_array($condition)){
            return in_array($param, $condition);
        }else{
            return $param == $condition;
        }
    }
}

<?php
namespace common\validators\product;

use common\models\Validator;
use yii\di\ServiceLocator;
use common\models\RapidQuery;
use common\ActiveRecord\ProductSKUAR;
use common\models\parts\ItemPrice;

class SkuValidator extends Validator{

    //商品id，设置后除了验证sku主键存在，商品id必须等于$this->productId
    public $productId;
    /**
     * sku可以包含的属性
     * sku携带的属性可少于$this->canContain
     * sku不可携带其他无关属性
     * 如果不设置该参数则不对sku的属性作验证
     *
     * 必须设置为array
     * 键名为属性名，
     * 键值需符合\yii\di\ServiceLocator::setComponents()
     */
    public $canContain;
    public $message;

    private $_validators;

    public function init(){
        parent::init();
        if(is_array($this->canContain)){
            $this->_validators = new ServiceLocator;
            $this->_validators->setComponents($this->canContain);
        }
    }

    /**
     * 验证商品sku
     * 若设置$this->productId,则必须是该商品所属的sku
     * 若设置$this->canContain,则对sku携带的参数逐个验证参数值
     */
    protected function validateValue($sku){
        $itemPrice = new ItemPrice;
        try{
            foreach($sku as $skuId => $skuData){
                $skuAR = ProductSKUAR::find()->where([
                    'id' => $skuId,
                ])->filterWhere([
                    'product_id' => $this->productId,
                ])->one();
                if(!$skuAR)throw new \Exception;
                if(is_array($this->canContain)){
                    foreach($skuData as $fieldName => $fieldValue){
                        if(!array_key_exists($fieldName, $this->canContain))throw new \Exception;
                        if(!$this->_validators->$fieldName->validate($fieldValue))throw new \Exception;
                    }
                }
                /* 修改：取消指导价必须大于单价限制
                if(isset($skuData['cost_price']) || isset($skuData['guidance_price'])){
                    $costPrice = $skuData['cost_price'] ?? $skuAR->cost_price;
                    $guidancePrice = $skuData['guidance_price'] ?? $skuAR->guidance_price;
                    $price = $itemPrice->generatePrice((float)$costPrice);
                    if(($costPrice > $price) || ($price >= $guidancePrice))throw new \Exception;
                }
                */
            }
            return true;
        }catch(\Exception $e){
            return $this->message;
        }
    }
}

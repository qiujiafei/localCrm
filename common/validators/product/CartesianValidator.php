<?php
namespace common\validators\product;

use common\models\Validator;
use common\models\parts\ProductSKUGenerator;
use common\traits\CartesianTrait;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;
use common\models\parts\ItemPrice;

/**
 * 笛卡尔积验证器
 */
class CartesianValidator extends Validator{

    use CartesianTrait;

    /**
     * 生成笛卡尔积的数据集合
     * 
     * 详见 \common\traits\CartesianTrait::generateCombo()
     */
    public $attrs;
    public $message;
    /**
     * 笛卡尔积对应具体数据的验证器
     *
     * 配置规则详见\yii\di\ServiceLocator::setComponents()
     */
    public $contain;

    public $validateSkuRule = false;

    //equal count($this->contain) 需要验证的参数个数
    private $_memberCount = 0;
    //由$this->contain生成的\yii\di\ServiceLocator实例
    private $_validators;

    public function init(){
        parent::init();
        if(is_array($this->contain)){
            $this->_memberCount = count($this->contain);
            $this->_validators = new ServiceLocator;
            $this->_validators->setComponents($this->contain);
        }
    }

    /**
     * 验证笛卡尔积是否正确
     *
     * 该验证为严格模式，每个笛卡尔积结果都必须存在
     *
     * 如果配置了$this->contain则会验证笛卡尔积数据，每个数据都必须通过验证
     *
     * @param array $outerCartesian
     * [
     *     'cartesian_id1' => ['attr1' => 'value1', 'attr2' => 'value2'],
     *     'cartesian_id2' => ['attr2' => 'value3', 'attr2' => 'value4'],
     * ]
     */
    protected function validateValue($outerCartesian){
        $itemPrice = new ItemPrice;
        try{
            if(!is_array($this->attrs))throw new InvalidConfigException;
            $combo = $this->generateCombo($this->attrs, ProductSKUGenerator::KEY_VALUE_SPLIT);
            $innerCartesian = $this->generateCartesianProduct($combo, ProductSKUGenerator::ATTRS_SPLIT);
            foreach($innerCartesian as $singleCartesian){
                $member = isset($outerCartesian[$singleCartesian]) ? $outerCartesian[$singleCartesian] : null;
                if(is_null($member))throw new \Exception;
                if($this->_memberCount){
                    $intersect = array_intersect_key($this->contain, $member);
                    if(count($intersect) != $this->_memberCount)throw new \Exception;
                    foreach($this->contain as $key => $value){
                        if(!$this->_validators->$key->validate($member[$key]))throw new \Exception;
                    }
                }
                if($this->validateSkuRule){
                    $costPrice = $outerCartesian[$singleCartesian]['cost_price'] ?? 0;
                    $guidancePrice = $outerCartesian[$singleCartesian]['guidance_price'] ?? 0;
                    $price = $itemPrice->generatePrice((float)$costPrice);
                    if(($costPrice > $price) || ($price >= $guidancePrice))throw new \Exception;
                }
            }
            return true;
        }catch(\Exception $e){
            return $this->message;
        }
    }
}

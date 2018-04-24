<?php
namespace common\traits;

trait CartesianTrait{

    /**
     * 生成$attrs key与value的全部组合
     *
     * @param array $attrs 属性与选项 详见common\models\parts\ProductSKUGenerator
     * @param string $split 分隔符
     *
     * @return array
     */
    protected function generateCombo($attrs, $split = ''){
        $loop = 0;
        $attrCombo = [];
        foreach($attrs as $attrId => $attr){
            foreach($attr as $options){
                foreach($options as $optionId => $option){
                    $attrCombo[$loop][] = $attrId . $split . $optionId;
                }
            }
            ++$loop;
        }
        return $attrCombo;
    }

    /**
     * 生成笛卡尔积结果
     *
     * @param array $multiArray self::generateCombo()返回值
     * @param string $split 分隔符
     *
     * @return array
     */
    protected function generateCartesianProduct($multiArray, $split = ''){
        if(!$firstArray = array_shift($multiArray))return false;
        if(!$secondArray = array_shift($multiArray))return $firstArray;
        foreach($firstArray as $firstCombo){
            foreach($secondArray as $secondCombo){
                $product[] = $firstCombo . $split . $secondCombo;
            }
        }
        if(empty($multiArray)){
            return $product;
        }else{
            array_unshift($multiArray, $product);
            return $this->generateCartesianProduct($multiArray, $split);
        }
    }
}

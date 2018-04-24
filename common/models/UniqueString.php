<?php
namespace common\models;

class UniqueString{

    //前缀、后缀 执行hash前添加
    public $prefix;
    public $suffix;

    //额外前缀、后缀 执行hash后添加
    public $extraPrefix;
    public $extraSuffix;

    /**
     * 随机模式
     *
     * time: 以当前时间戳生成随机数，当生成多个时需添加前、后缀
     * microtime: 以时间戳和微妙数生成随机数
     * float: 以microtime 的浮点数形式生成随机数
     *
     * microtime和float 当生成多个随机数时，依平台(windows)的不同实现可能会执行usleep(1)
     */
    public $mode = 'time';

    //上次生成的随机值
    private $lastRandomString;

    //实例化时传入属性，不传则以默认模式输出值
    public function __construct(array $attrs = null){
        if(!is_null($attrs)){
            foreach($attrs as $attrName => $attrValue){
                if(property_exists(__CLASS__, $attrName)){
                    $this->{$attrName} = $attrValue;
                }
            }
        }
    }

    /**
     * 生成随机值
     * 
     * @param $num 生成数量
     * @return string | array
     */
    public function string($num = 1){
        return $this->generate('string', $num);
    }

    /**
     * 生成随机哈希
     *
     * @param $num 生成数量
     * @return string | array
     */
    public function hash($num = 1, $type = 'sha1'){
        return $this->generate($type, $num);
    }

    /**
     * 获取随机值
     *
     * @return string
     */
    protected final function getRandomString(){
        switch($this->mode){
            case 'time':
                $random = time();
                break;

            case 'float':
                $random = microtime(true);
                if($random == $this->lastRandomString){
                    usleep(1);
                    $random = microtime(true);
                }
                break;

            case 'microtime':
                $random = microtime();
                if($random == $this->lastRandomString){
                    usleep(1);
                    $random = microtime(true);
                }
                break;

            default:
                throw new \Exception('unknown mode');
                break;
        }
        $this->lastRandomString = $random;
        return $this->addAffix($random);
    }

    /**
     * 添加前、后缀
     *
     * @return string
     */
    protected function addAffix($randomString){
        $string = empty($this->prefix) ? $randomString : $this->prefix . $randomString;
        return $this->addSuffix($string, $this->suffix);
    }

    /**
     * 添加后缀
     *
     * 当$suffix为 1-9 的数字时会添加$suffix位数的随机数
     * 否则将$suffix作为字符串添加
     * @return string
     */
    protected function addSuffix($randomString, $suffix){
        if(empty($suffix)){
            return $randomString;
        }else{
            if(is_numeric($suffix) && $suffix >= 1 && $suffix <= 9){
                return $randomString . $this->getRandomInteger($suffix);
            }else{
                return $randomString . $suffix;
            }
        }
    }

    /**
     * 获取随机数
     *
     * @return integer
     */
    protected function getRandomInteger($digit){
        $min = '1';
        $max = '9';
        for($i = 1; $i < $digit; ++$i){
            $min = $min . '0';
            $max = $max . '9';
        }
        $min = (int)$min;
        $max = (int)$max;
        return rand($min, $max);
    }

    /**
     * 添加额外前、后缀
     *
     * @return string | array
     */
    protected function addExtraAffix($randomString){
        if(is_array($randomString)){
            return array_map(function($string){
                return $this->addSuffix($this->extraPrefix . $string, $this->extraSuffix);
            }, $randomString);
        }else{
            return $this->addSuffix($this->extraPrefix . $randomString, $this->extraSuffix);
        }
    }

    /**
     * 生成随机值
     *
     * @return string | array
     */
    protected function generate($type, $num){
        if($num == 1){
            $randomString = $this->getRandomString();
        }else if($num > 1){
            for($i = 0; $i < $num; ++$i){
                $randomString[] = $this->getRandomString();
            }
        }else{
            throw new \Exception('must be a positive integer');
        }
        if($type != 'string'){
            $randomString = $this->convertIntoHash($randomString, $type);
        }
        if(empty($this->extraPrefix)){
            return $randomString;
        }else{
            return $this->addExtraAffix($randomString);
        }
    }

    /**
     * 转换随机值成哈希值
     *
     * @return string | array
     */
    protected function convertIntoHash($randomString, $type){
        if(is_array($randomString)){
            return array_map(function($string)use($type){
                return hash($type, $string);
            }, $randomString);
        }else{
            return hash($type, $randomString);
        }
    }

}

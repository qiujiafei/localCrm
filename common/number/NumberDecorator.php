<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/26
 * Time: 15:04
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description 定义编号工厂批量生产
 */
namespace common\number;
class NumberDecorator implements NumberInterface
{
    //可接受供应商来源
    const ACCEPT_ORIGIN_NUMBER = ['01','02'];
    //采购，入库，异常，盘点
    protected static $type;
    //来源，01为九大爷，02为其他供应商
    protected static $origin;

    protected $numberObj;

    public function __construct($origin='01')
    {
        $this->resolveOrigin($origin);
    }

    /**
     * 对来源参数进行格式化处理
     * @param $origin 来源值
     * @return null
     * @throws \Exception
     */
    private function resolveOrigin($origin)
    {
        if (strlen($origin) != 2){
            $origin = str_pad($origin,2,'0',STR_PAD_LEFT);
        }
        if ( ! in_array($origin,self::ACCEPT_ORIGIN_NUMBER, true)){
            throw new \Exception('来源异常',12007);
        }
        static::$origin = $origin;
        return null;
    }

    public function getNumber()
    {
        return static::generateNumber();
    }

    /**
     *  生成编号，允许子类重写
     * @return string
     * @throws \Exception
     */
    protected function generateNumber()
    {
        $data = [
            date('Ymd'),
            static::$origin,
            static::$type,
            static::generateSuffixNumber()
        ];

        return implode('',$data);
    }

    /**
     * 创建单号后10位
     * @param int $length  需要数字长度
     * @return int|string
     * @throws \Exception
     */
    protected function generateSuffixNumber($length = 10)
    {
        $number = random_int(1,pow(10,$length) - 1);
        if (strlen($number) != $length) {
            $number = str_pad($number,10,STR_PAD_LEFT);
        }
        return $number;
    }
}
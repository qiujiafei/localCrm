<?php
namespace common\components;

class RapidTime{

    /**
     * 获取时间
     *
     * @return str | int
     */
    public function __get($name){
        switch($name){
            case 'fullDate':
                $time = date('Y-m-d H:i:s');
                break;

            case 'onlyDate':
                $time = date('Y-m-d');
                break;

            case 'fullTime':
                $time = date('H:i:s');
                break;

            case 'year':
                $time = date('Y');
                break;

            case 'month':
                $time = date('m');
                break;

            case 'week':
                $time = date('w');
                break;

            case 'day':
                $time = date('d');
                break;

            case 'hour':
                $time = date('H');
                break;

            case 'minute':
                $time = date('i');
                break;

            case 'second':
                $time = date('s');
                break;

            case 'unixTime';
                $time = time();
                break;

            case 'microTime':
                $time = microtime();
                break;

            case 'millisecond':
                $time = $this->getMillisecond();
                break;

            default:
                throw new \Exception('no such time');
        }
        return $time;
    }

    protected function getMillisecond(){
        list($extraTime, $unixTime) = explode(' ', $this->microTime);
        return (float)($extraTime * 1000);
    }
}

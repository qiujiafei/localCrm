<?php
namespace common\traits;

trait ErrCallbackTrait{

    protected static function errCallback($return = 'throw', $exceptionMsg = '', $exception = '\Exception'){
        if($return === 'throw'){
            if($exceptionMsg instanceof \Exception){
                $exceptionObj = $exceptionMsg;
            }elseif($exception instanceof \Exception){
                $exceptionObj = $exception;
            }else{
                $exceptionObj = (new \ReflectionClass($exception))->newInstance(self::getErrMsg($exceptionMsg));
            }
            throw $exceptionObj;
        }else{
            return $return;
        }
    }

    protected static function getDefaultErrMsg(){
        return [
            'P_int' => 'require positive integer',
            'P_float' => 'require positive float',
            'string' => 'require non empty string',
            'mysql' => 'mysql query failed',
            'array' => 'require non empty array',
            'guest' => 'the user must login',
        ];
    }

    protected static function getErrMsg($msg){
        return self::getDefaultErrMsg()[$msg] ?? $msg;
    }
}

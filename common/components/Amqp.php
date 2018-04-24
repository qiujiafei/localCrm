<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use common\components\amqp\Extlib;
use common\components\amqp\Phplib;
use common\components\amqp\Message;

class Amqp extends Component{

    const MODE_EXTENSION = 'extension';
    const MODE_PHPLIB = 'phplib';

    public $host;
    public $port;
    public $user;
    public $passwd;
    public $vhost;
    public $initialize = true;
    public $mode;

    private $_mode;
    private static $_amqp;

    public function init(){
        if(is_null($this->mode)){
            if(extension_loaded('amqp')){
                $this->_mode = self::MODE_EXTENSION;
            }else{
                $this->_mode = self::MODE_PHPLIB;
            }
        }else{
            if(in_array($this->mode, [self::MODE_EXTENSION, self::MODE_PHPLIB])){
                $this->_mode = $this->mode;
            }else{
                throw new InvalidConfigException('unavailable amqp mode');
            }
        }
        $amqpConstructConfig = [
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'passwd' => $this->passwd,
            'vhost' => $this->vhost,
        ];
        switch($this->_mode){
            case self::MODE_EXTENSION:
                self::$_amqp = new Extlib($amqpConstructConfig);
                break;

            case self::MODE_PHPLIB:
                self::$_amqp = new Phplib($amqpConstructConfig);
                break;

            default:
                throw new \Exception;
        }
        if($this->initialize)self::$_amqp->initialize();
    }

    /**
     * 获取具体amqp对象
     *
     * @return Object \common\components\amqp\Extlib or \common\components\amqp\Phplib
     */
    public function getAmqp(){
        return self::$_amqp;
    }

    /**
     * 推送消息至rabbitmq
     *
     * @param Object $message 消息对象
     *
     * @return mix
     */
    public function publish(Message $message){
        return self::$_amqp->publish(serialize($message->get()));
    }

    /**
     * 从rabbitmq获取一条消息
     *
     * @return array
     */
    public function get(){
        if($message = self::$_amqp->get()){
            $message['body'] = unserialize($message['body']);
        }
        return $message;
    }

    /**
     * 向rabbitmq报告指定消息已收到
     *
     * @return mix
     */
    public function ack(int $tag){
        return self::$_amqp->ack($tag);
    }

    /**
     * 获取模式
     *
     * @return string
     */
    public function getMode(){
        return $this->_mode;
    }

    /**
     * 关闭php断开连接
     */
    public function __distruct(){
        self::$_amqp->close();
    }
}

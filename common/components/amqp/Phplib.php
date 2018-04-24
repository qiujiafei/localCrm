<?php
namespace common\components\amqp;

use Yii;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Phplib extends LibAbstract{

    public function init(){
        if(!self::$connection){
            parent::init();
            self::$connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->passwd, $this->vhost);
            self::$channel = self::$connection->channel();
        }
    }

    /**
     * 初始化设置；
     *
     * 设置交换机、队列、队列绑定
     */
    public function initialize(){
        self::$channel->exchange_declare($this->exchangeName, 'direct', false, true, false);
        self::$channel->queue_declare($this->queueName, false, true, false, false);
        self::$channel->queue_bind($this->queueName, $this->exchangeName, $this->routeKey);
    }

    public function publish(string $message){
        $amqpMessage = new AMQPMessage($message, ['delivery_mode' => 2]);
        return self::$channel->basic_publish($amqpMessage, $this->exchangeName, $this->routeKey);
    }

    public function get(){
        if($obj = self::$channel->basic_get($this->queueName)){
            return [
                'obj' => $obj,
                'body' => $obj->body,
                'delivery_tag' => $obj->delivery_info['delivery_tag'],
            ];
        }else{
            return null;
        }
    }

    public function ack(int $tag){
        return self::$channel->basic_ack($tag);
    }

    public function close(){
        self::$channel->close();
        self::$connection->close();
    }
}

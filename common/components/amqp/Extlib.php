<?php
namespace common\components\amqp;

use Yii;
use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;

class Extlib extends LibAbstract{

    protected static $exchange;
    protected static $queue;

    public function init(){
        if(!self::$connection){
            parent::init();
            self::$connection = new AMQPConnection([
                'host' => $this->host,
                'port' => $this->port,
                'login' => $this->user,
                'password' => $this->passwd,
                'vhost' => $this->vhost,
            ]);
            if(!self::$connection->connect())throw new \Exception('unable to connect rabbitmq server');
            self::$channel = new AMQPChannel(self::$connection);
            self::$exchange = new AMQPExchange(self::$channel);
            self::$queue = new AMQPQueue(self::$channel);
        }
    }

    /**
     * 初始化设置；
     * 
     * 设置交换机、队列；队列绑定
     */
    public function initialize(){
        self::$exchange->setName($this->exchangeName);
        self::$exchange->setType(AMQP_EX_TYPE_DIRECT);
        self::$exchange->setFlags(AMQP_DURABLE);
        self::$exchange->declareExchange();
        self::$queue->setName($this->queueName);
        self::$queue->setFlags(AMQP_DURABLE);
        self::$queue->declareQueue();
        self::$queue->bind($this->exchangeName, $this->routeKey);
    }

    /**
     * 获取交换机对象
     *
     * @return Object
     */
    public function getExchange(){
        return self::$exchange;
    }

    /**
     * 获取队列对象
     *
     * @return Object
     */
    public function getQueue(){
        return self::$queue;
    }

    public function publish(string $message){
        return self::$exchange->publish($message, $this->routeKey, AMQP_NOPARAM, ['delivery_mode' => 2]);
    }

    public function get(){
        if($obj = self::$queue->get()){
            return [
                'obj' => $obj,
                'body' => $obj->getBody(),
                'delivery_tag' => $obj->getDeliveryTag(),
            ];
        }else{
            return null;
        }
    }

    public function ack(int $tag){
        return self::$queue->ack($tag);
    }

    public function close(){
        self::$connection->disconnect();
    }
}

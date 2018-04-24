<?php
namespace common\components\amqp;

use Yii;
use yii\base\Object;
use yii\base\InvalidConfigException;

abstract class LibAbstract extends Object{

    public $host;
    public $port;
    public $user;
    public $passwd;
    public $vhost;

    public $exchangeName = 'php_backend_exchange';
    public $queueName = 'php_backend_queue';
    public $routeKey = 'php_backend_routekey';

    protected static $connection;
    protected static $channel;

    public function init(){
        if(is_null($this->host) ||
            is_null($this->port) ||
            is_null($this->user) ||
            is_null($this->passwd) ||
            is_null($this->vhost))throw new InvalidConfigException('incomplete configuration');
    }

    public function getConnection(){
        return self::$connection;
    }

    public function getChannel(){
        return self::$channel;
    }

    abstract public function initialize();

    abstract public function publish(string $message);

    abstract public function get();

    abstract public function ack(int $tag);

    abstract public function close();
}

<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components;

use common\exceptions;
use common\components\ContainerInterface;

/**
 * Active Record locator, like Psr\Container, it has 2 main interface, get() & has()
 */
class ActiveRecordLocator implements ContainerInterface
{
    /**
     * ActiveRecord namespace
     *
     * @var string
     */
    private $namespace = 'common\ActiveRecord';

    /**
     * suffix of ActiveRecord class name
     *
     * @var string
     */
    private $suffix = 'AR';

    /**
     * prefix of ActiveRecord class name
     *
     * @var string
     */
    private $prefix = '';

    /**
     * container of ActiveRecord instance
     *
     * @var object
     */
    private $instance;

    /**
     * get a ActiveRecord instance
     *
     * @param $name string
     * @return object | null
     */
    public function get($name)
    {
        $className = $this->resolveName($name);

        if(isset($this->instance[$className])) {
            return $this->instance[$className];
        }

        return $this->build($className);
    }

    /**
     * get a ActiveRecord static string
     *
     * @param $name string
     * @return string | null
     */
    public function getStatic($name)
    {
        $className = $this->resolveName($name);

        if(class_exists($className)) {
           return $className; 
        }

        return null;
    }

    /**
     * get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * set namespace
     *
     * @throws exceptions\InvalidArgumentException
     * @param $namespace string
     * @return object
     */
    public function setNamespace($namespace)
    {
        if(empty($nameapace) || !is_string($namespace)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'namespace as a argument should be string. %s or null given. In %s.',
                gettype($namespace),
                __METHOD__           
            ));
        }
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * set prefix
     *
     * @throws exceptions\InvalidArgumentException
     * @param $prefix string
     * @return object
     */
    public function setPrefix($prefix)
    {
        if(empty($prefix) || !is_string($prefix)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'prefix as a argument should be string. %s or null given. In %s.',
                gettype($prefix),
                __METHOD__           
            ));
        }
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * get surfix
     *
     * @return string
     */
    public function getSurfix()
    {
        return $this->surfix;
    }

    /**
     * set surfix
     *
     * @throws exceptions\InvalidArgumentException
     * @param $surfix string
     * @return object
     */
    public function setSurfix($surfix)
    {
        if(empty($surfix) || !is_string($surfix)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'surfix as a argument should be string. %s or null given. In %s.',
                gettype($surfix),
                __METHOD__           
            ));
        }
        $this->surfix = $surfix;
        return $this;
    }

    /**
     * resolve ActiveRecord name of given string
     *
     * @throws exceptions\InvalidArgumentException
     * @param $tableName string
     * @return string
     */
    private function resolveName($tableName)
    {
        if(empty($tableName) || !is_string($tableName)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'tableName as a argument should be string. %s or null given. In %s.',
                gettype($tableName),
                __METHOD__
            ));
        }

        $splite = explode('_', $tableName);

        $resolvedTableName = '';

        foreach($splite as $v) {
            if(! empty($v)) 
                $resolvedTableName .= ucfirst($v);
        }

        $resolvedTableName = $this->addFix($resolvedTableName);

        return $this->addNamespace($resolvedTableName);
    }

    /**
     * add bose surfix and prefix onto given string
     *
     * @param $name string
     * @return string
     */
    private function addFix($name)
    {
        if(!empty($this->suffix))
            $name .= $this->suffix;

        if(!empty($this->prefix))
            $name = $this->prefix . $name;

        return $name;
    }

    /**
     * add namespace onto given string
     * 
     * @param $tanleName string
     * @return string
     */
    private function addNamespace($tableName)
    {
        return $this->namespace . '\\' . $tableName;
    }

    /**
     * check if given string stands for a ActiveRecord object
     *
     * @param $name string
     * @return bool
     */
    public function has($name)
    {
        $className = $this->resolveName($name);

        if(isset($this->instance[$className]) || class_exists($className)) {
            return true;
        }

        return false;
    }

    /**
     * build a ActiveRecord instance and set it into container
     *
     * @throws exceptions\InvalidArgumentException
     * @param $class string
     * @return object | null
     */
    public function build($class)
    {
        if(empty($class) || !is_string($class)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'class as a argument should be string. %s or null given. In %s.',
                gettype($tableName),
                __METHOD__
            ));
        }

        if(!class_exists($class)) {
            return null;
        }

        $this->instance[$class] = new $class;

        return $this->instance[$class];
    }
}

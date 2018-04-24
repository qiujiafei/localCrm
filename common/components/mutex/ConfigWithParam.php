<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\mutex;

use yii\db\ActiveRecord;
use common\components\mutex\exceptions\InvalidConfigException;

/**
 * Config with params
 */
class ConfigWithParam implements ConfigInterface
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Constructor
     *
     * @param integer|array|yii\db\ActiveRecord|object|closure $id
     * @param string|yii\db\ActiveRecord|object|closure $tableName
     * @throws exceptions\CannotGetLockException
     * @throws exceptions\CannotReleaseLockException
     * @return void
     */
    public function __construct($id = null, $tableName = null)
    {
        if($id !== null) {
            $this->configId($id);
        }
        if($tableName !== null) {
            $this->configTableName($tableName);
        }
    }

    public function isInt($int)
    {
        if(is_int($int)) {
            return true;  
        } elseif(is_string($int)) {
            if((string)(int)$int === $int) {
                return true;
            }
        }
        return false;
    }

    /**
     * Config id into mutex.
     *
     * @param integer|array|yii\db\ActiveRecord|object|closure $id
     * @throws InvalidConfigException
     * @return integer
     */ 
    public function configId($id)
    {
        if(is_array($id) && array_key_exists('id', $id)) {
            $id = $id['id'];
        }

        if($id instanceof ActiveRecord) {
            $id = $id->id;
        }

        if(is_object($id) && method_exists($id, 'getId')) {
            $id = $id->getId();
        }

        if(is_callable($id)) {
            $id = $id($this);
        }

        if($this->isInt($id)) {
            $this->id = $id;
            return $id;
        }

        throw new InvalidConfigException(sprintf(
            'Invalid id given. In %s.',
            __METHOD__
        ));
    }

    /**
     * Config table name.
     *
     * @param string|yii\db\ActiveRecord|object|closure $tableName
     * @throws InvalidConfigException
     * @return string
     */
    public function configTableName($tableName)
    {
        if($tableName instanceof ActiveRecord) {
            $tableName = $tableName->getTableSchema()->fullName;
        }

        if(is_object($tableName) && method_exists($tableName, 'getTableName')) {
            $tableName = $tableName->getTableName();
        }

        if(is_callable($tableName)) {
            $tableName = $tableName($this);
        }

        if(is_string($tableName)) {
            $this->tableName = $tableName;
            return $tableName;
        }

        throw new InvalidConfigException(sprintf(
            'Invalid table name given. In %s.',
            __METHOD__
        ));

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return isset($this->id) ?? false;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName()
    {
        return isset($this->tableName) ?? false;
    }
}

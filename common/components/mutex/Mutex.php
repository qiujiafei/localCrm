<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace common\components\mutex;

use common\components\mutex\exceptions;
use common\components\mutex\dbHandler;
use yii\db\ActiveRecord;

/**
 * Mutual exclusion based on DB, to lock DB recored row
 *
 * use it's id and table name.
 */
class Mutex
{
    /**
     * @var integer
     */
    const TIMES_TO_TRY = 1000;

    /**
     * Record ID
     *
     * @var integer
     */
    protected $id;

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Id and table name config
     *
     * @var object
     */
    protected $config;

    /**
     * Timeout secouds
     *
     * @var timeout
     */
    protected $timeout = 10;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param integer $timeout
     * @return void
     */
    public function __construct(ConfigInterface $config, integer $timeout = null)
    {
        if($timeout >= 0 && $timeout !== null) {
            $this->timeout = $timeout;
        }

        $this->config = $config;
    }

    /**
     * Release a lock
     *
     * @param integer|array|yii\db\ActiveQuery|object|closure $id
     * @param string|yii\db\ActiveRecord|object|closure $tableName
     * @return bool
     */
    public function release($id, $tableName = null)
    {
        $this->config($id, $tableName);
        try {
            dbHandler\delete\DeleteMutex::deleteMutex($this->id, $this->tableName);
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    /**
     * Get a lock
     *
     * @param integer|array|yii\db\ActiveQuery|object|closure $id
     * @param string|yii\db\ActiveRecord|object|closure $tableName
     * @return bool
     */
    public function get($id, $tableName = null)
    {
        $this->config($id, $tableName);
        $times = self::TIMES_TO_TRY;
        while($times > 0) {
            try {
                dbHandler\delete\DeleteMutex::deleteExpiredMutex($this->id, $this->tableName, $this->timeout);
            } catch (\Exception $ex) {
                $this->error[] = $ex;
            }
            if(dbHandler\put\PutMutex::putMutex($this->id, $this->tableName)) {
                return true;
            } else {
                $times--;
            }
        }
        return false;
    }

    /**
     * Config params init this object.
     *
     * @param integer|array|yii\db\ActiveQuery|object|closure $id
     * @param string|yii\db\ActiveRecord|object|closure $tableName
     * @return bool
     */
    private function config($id, $tableName)
    {
        if($id instanceof ActiveRecord) {
            $tableName = $id;
        }

        $this->id = $this->config->configId($id);
        $this->tableName = $this->config->configTableName($tableName);

        return false;   
    }
}

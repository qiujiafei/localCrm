<?php

/**
 * CRM system for 9daye
 *
 * @author Vett <hejinsong@9daye.com.cn>
 */

namespace commodity\models\interfaces;

interface ValidateDepotIdInterface extends DepotInterface
{
    /**
     * @param $depotId
     * @return bool
     */
    public static function isValidDepotId($depotId);
}
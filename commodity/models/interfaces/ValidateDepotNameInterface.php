<?php
/**
 * CRM system for 9daye
 *
 * @author Vett <hejinsong@9daye.com.cn>
 */

namespace commodity\models\interfaces;

/**
 * 需要仓库管理模块提供的接口
 */

interface ValidateDepotNameInterface extends DepotInterface
{
    public static function isValidDepot($depotName);
}
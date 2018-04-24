<?php
/**
 * CRM system for 9daye
 *
 * @author: wj <wangjie@9daye.com.cn>
 */

namespace commodity\modules\memberPoint\models\memberPoint\put;

use commodity\activeRecord\MemberPointAR;
use commodity\activeRecord\MemberPointRateAR;
use commodity\activeRecord\MemberPointLogAR;
use Yii;
use yii\db\Query;

class Insert
{
    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function insertRate(array $condition)
    {
        $db = \Yii::$app->db->createCommand()
            ->insert('crm_member_point_rate', $condition)
            ->execute();

        return $db;
    }


}
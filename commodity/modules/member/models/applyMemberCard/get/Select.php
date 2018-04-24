<?php
/**
 * CRM system for 9daye
 *
 * @author: zhuangzhuang <qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard\get;


use commodity\activeRecord\MemberCardAR;

use yii\data\ActiveDataProvider;
use Yii;
use yii\db\Query;

class Select {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;



    //验证卡号数据存在不存在
    public static function getField(array $condition, $field) {

        return MemberCardAR::find()->select($field)->where($condition)->one();

    }


}
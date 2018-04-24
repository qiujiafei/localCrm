<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\frontBridge\models\get;

use common\models\Model as CommonModel;
use commodity\modules\frontBridge\models\get\db\Insert;
use commodity\modules\frontBridge\models\get\db\Select;
use commodity\modules\customerinfomation\models\api\CustomForFrontBridge as Custom;
use commodity\modules\finance\models\interfaces\TodayFinanceProfile;
use Yii;

class GetModel extends CommonModel
{

    const ACTION_GET_PROFILE = 'action_get_profile';

    public function scenarios()
    {
        return [
            self::ACTION_GET_PROFILE => [],
        ];
    }

    public function actionGetProfile()
    {
        $custom = new Custom();

        $finance = new TodayFinanceProfile();

        return [
            'custom_in_bill' => $custom->memberCount(),
            'custom' => $custom->memberCount()['member_count'],
            'custom_month_increse' => $custom->monthMemberIncrese()['member_count'],
            'today_finance_profile' => $finance->getToady(),
        ];
    }
}

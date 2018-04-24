<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models\get;

use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\purchase\models\get\db\SelectModel;


class StatisticsModel extends CommonModel
{
    const ACTION_TODAY = 'action_today';
    const ACTION_MONTH = 'action_month';
    const ACTION_TOTAL   = 'action_total';
    const ACTION_ALL   = 'action_all';

    public function scenarios() {
        return [
            self::ACTION_TODAY => [],
            self::ACTION_MONTH => [],
            self::ACTION_TOTAL => [],
            self::ACTION_ALL => [],
        ];
    }

    /**
     * 今天采购
     */
    public function actionToday()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $price = SelectModel::getStatisticsByToday($storeId);
        return ['price' => $price];
    }

    /**
     * 本月1日凌晨到现在的采购
     */
    public function actionMonth()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $price = SelectModel::getStatisticsByMonth($storeId);
        return ['price' => $price];
    }

    /**
     * 所有采购金额，不计算折扣
     * @return array
     */
    public function actionTotal()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $price = SelectModel::getStatisticsByAll($storeId);
        return ['price' => $price];
    }

    /**
     * 数据总接口，返回需要的所有值
     * @return array
     */
    public function actionAll()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $data = [];
        $data['today'] = SelectModel::getStatisticsByToday($storeId);
        $data['month'] = SelectModel::getStatisticsByMonth($storeId);
        $data['total'] = SelectModel::getStatisticsByAll($storeId);
        return $data;
    }
}
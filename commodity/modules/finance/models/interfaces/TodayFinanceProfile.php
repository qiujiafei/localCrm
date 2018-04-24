<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/27
 * Time: 15:36
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 *
 */
namespace commodity\modules\finance\models\interfaces;

use commodity\modules\bill\models\get\db\SelectModel;
use commodity\modules\finance\models\get\db\FinanceTurnoverSelectModel;
use commodity\modules\frontBridge\models\interfaces\FinanceProfileInterface;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class TodayFinanceProfile implements FinanceProfileInterface
{
    /**
     * 今日营业额
     * @return number
     */
    public function getToady()
    {
        //获取门店ID
        $storeId = AccessTokenAuthentication::getUser(true);
        $where['store_id'] = $storeId;
        $times =
            [
                date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))),
                date('Y-m-d H:i:s')
            ];

        $model = new SelectModel();
        return $model->getTurnoverByToday($where['store_id'],$times[0],$times[1]);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 13:31
 */

namespace commodity\modules\finance\models\get;

use commodity\modules\finance\models\get\db\BillSelectModel;
use commodity\modules\finance\models\get\db\CommodityBatchSelectModel;
use commodity\modules\finance\models\get\db\CustomerInformationSelectModel;
use commodity\modules\finance\models\get\db\FinancePurchaseSelectModel;
use commodity\modules\finance\models\get\db\FinanceTurnoverSelectModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;

class GetModel extends CommonModel
{
    //营业额
    const ACTION_TURNOVER = 'action_turnover';
    //采购金额
    const ACTION_PURCHASE_AMOUNT = 'action_purchase_amount';
    //采购应付
    const ACTION_PURCHASE_SUPPLIER = 'action_purchase_supplier';
    //到店次数
    const ACTION_FREQUENCY_OF_STORE_VISIT = 'action_frequency_of_store_visit';
    //客户
    const ACTION_CUSTOMERS = 'action_customers';
    //营业额列表明细
    const ACTION_TURNOVER_LISTS = 'action_turnover_lists';
    //采购金额明细
    const ACTION_PURCHASE_AMOUNT_LISTS = 'action_purchase_amount_lists';
    //到店次数明细
    const ACTION_FREQUENCY_OF_STORE_VISIT_LISTS = 'action_frequency_of_store_visit_lists';
    //采购统计
    const ACTION_PURCHASE_STATISTICS_LISTS = 'action_purchase_statistics_lists';
    //施工统计
    const ACTION_SERVICE_LISTS = 'action_service_lists';

    public $token;
    //月份阶梯
    private $monthLadder = [];
    //日阶梯
    private $daysLadder = [];

    public $startDate;
    public $endDate;

    public $page;
    public $pageSize;
    public $searchTime;  //搜索时间，用于快速搜索，是一个返回量

    public $supplier_id;

    public function scenarios()
    {
        return [
            self::ACTION_TURNOVER => ['token'],
            self::ACTION_PURCHASE_AMOUNT => ['token'],
            self::ACTION_PURCHASE_SUPPLIER => ['token','startDate','endDate','searchTime','supplier_id'],
            self::ACTION_FREQUENCY_OF_STORE_VISIT => ['token'],
            self::ACTION_CUSTOMERS => ['token'],
            self::ACTION_TURNOVER_LISTS => ['token','startDate','endDate','page','pageSize','searchTime'],
            self::ACTION_PURCHASE_AMOUNT_LISTS => ['token','startDate','endDate','page','pageSize','searchTime'],
            self::ACTION_FREQUENCY_OF_STORE_VISIT_LISTS => ['token','startDate','endDate','page','pageSize','searchTime'],
            self::ACTION_PURCHASE_STATISTICS_LISTS => ['token','startDate','endDate','page','pageSize','searchTime'],
            self::ACTION_SERVICE_LISTS => ['token','startDate','endDate','page','pageSize','searchTime'],
        ];
    }

    //统计当前日，及其前30日的营业额数据
    public function actionTurnover()
    {
        try
        {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $currentYMD = $this->getCurrentYMD();

            //创建月份阶梯
            $this->createDaysLadder($currentYMD,30);

            $where = [
                'store_id' => $storeId
            ];

            $financeModel = new FinanceTurnoverSelectModel();
            return $financeModel->createStatisticsDataOfMonth($this->daysLadder,$where);
        }

        catch (\Exception $e) {

            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    //统计当月至前12个月的采购数据，按月递增显示
    public function actionPurchaseAmount()
    {
        try
        {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $currentMonth = $this->getCurrentMonth();

            //创建月份阶梯
            $this->createMonthLadder($currentMonth,12);

            $where = [
                'store_id' => $storeId
            ];

            $financeModel = new FinancePurchaseSelectModel();
            return $financeModel->createStatisticsDataOfMonth($this->monthLadder,$where);
        }

        catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

    //统计当月至前12个月的客户到店次数数据，按月递增显示
    public function actionFrequencyOfStoreVisit()
    {
        try
        {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $currentMonth = $this->getCurrentMonth();

            //创建月份阶梯
            $this->createMonthLadder($currentMonth,12);

            $where = [
                'store_id' => $storeId
            ];

            $billModel = new BillSelectModel();
            return $billModel->createStatisticsDataOfMonth($this->monthLadder,$where);
        }

        catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    //统计当月至前12个月的客户数量，按月递增显示
    public function actionCustomers()
    {
        try
        {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $currentMonth = $this->getCurrentMonth();

            //创建月份阶梯
            $this->createMonthLadder($currentMonth,12);

            $customerModel = new CustomerInformationSelectModel();
            return $customerModel->createStatisticsDataOfMonth($this->monthLadder,$storeId);
        }

        catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 营业额列表明细
     */
    public function actionTurnoverLists()
    {
        try {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $this->resolveTimeInterval();

            $where = [
                'store_id' => $storeId,
                'between' => [$this->startDate,$this->endDate]
            ];

            $billModel = new BillSelectModel();
            $lists = $billModel->getListByService($where,$this->pageSize);
            return $lists;
        }
        catch (\Exception $e) {

            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 采购金额明细
     */
    public function actionPurchaseAmountLists()
    {
        try {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $this->resolveTimeInterval();
            $financeModel = new FinancePurchaseSelectModel();
            $where = [
                'store_id' => $storeId,
                'between' => ['between','fp.created_time',$this->startDate,$this->endDate]
            ];
            $lists = $financeModel->findListsByCommodity($where,$this->pageSize);
            return $lists;
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 到店明细
     * @return array|bool
     */
    public function actionFrequencyOfStoreVisitLists()
    {
        try {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $this->resolveTimeInterval();
//            $where = [
//                'store_id' => $storeId,
//                'between' => ['between','b.created_time',$this->startDate,$this->endDate]
//            ];

            $billModel = new BillSelectModel();
            return $billModel->findListByFrequencyOfStore($storeId,$this->startDate,$this->endDate,$this->pageSize);
        }
        catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 采购应付，显示该门店在筛选的时间段内在所有成功入库的单据中，统计在各供应商下采购商品的金额总数。
     * @return array
     */
    public function actionPurchaseSupplier()
    {
        try {
            //获取门店ID
            $storeId = AccessTokenAuthentication::getUser(true);
            $this->resolveTimeInterval();
//            $where = [
//                'and',
//                ['store_id' => $storeId],
//                ['between','created_time',$this->startDate,$this->endDate]
//            ];
//
//            if ($this->supplier_id) {
//                $where[] = ['supplier_id' => $this->supplier_id];
//            }

            $model = new CommodityBatchSelectModel();
            return $model->findListOfSupplier($storeId,$this->startDate,$this->endDate,$this->supplier_id);
        }
        catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 采购统计列表
     * @return array
     */
    public function actionPurchaseStatisticsLists()
    {
        //获取门店ID
        $storeId = AccessTokenAuthentication::getUser(true);
        $this->resolveTimeInterval();
        $model = new FinancePurchaseSelectModel();

        $list = $model->findListByPurchaseCommodityOfStore($storeId,$this->startDate,$this->endDate,$this->pageSize);
        return $list;
    }

    /**
     * 施工统计
     * 服务项目，服务售价，施工次数，施工占比
     */
    public function actionServiceLists()
    {
        //获取门店ID
        $storeId = AccessTokenAuthentication::getUser(true);
        $this->resolveTimeInterval();
        $where = [
            'store_id' => $storeId,
            'between' => [$this->startDate,$this->endDate]
        ];

        $billModel = new BillSelectModel();
        $lists = $billModel->getListByServiceStatistics($where,$this->pageSize);
        return $lists;
    }

    /**
     * 获取当前年月
     * @return false|string
     */
    private function getCurrentMonth()
    {
        return date('Y-m');
    }

    /**
     * 获取当前年月日
     * @return false|string
     */
    private function getCurrentYMD()
    {
        return date('Y-m-d');
    }

    /**
     * 获取多个月前
     * @param $currentMonth
     * @param int $monthAgo
     * @return false|string
     */
    private function getMonthsAgo($currentMonth,$monthAgo=0)
    {
        return date('Y-m',strtotime('-'.($monthAgo).' month', strtotime($currentMonth)));
    }

    /**
     * 获取多少天前
     * @param $currentDay
     * @param int $dayAgo
     * @return false|string
     */
    private function getDaysAgo($currentDay,$dayAgo=0)
    {
        return date('Y-m-d',strtotime('-'.($dayAgo).' days', strtotime($currentDay)));
    }

    /**
     * 创建月份阶梯
     * @param $maxMonth
     * @param int $ladder
     * @return array
     */
    private function createMonthLadder($maxMonth,$ladder=0)
    {
        $return = [];
        $ladder -= 1;//减去当前月
        while ($ladder) {
            $return[] = $this->getMonthsAgo($maxMonth,$ladder);
            $ladder -= 1;
        }
        $return[] = $maxMonth;
        $this->monthLadder = $return;
        return $return;
    }

    /**
     * 创建日阶梯
     * @param $maxDays
     * @param int $ladder
     * @return array
     */
    private function createDaysLadder($maxDays,$ladder=0)
    {
        $return = [];
        $ladder -= 1;//减去当前日
        while ($ladder) {
            $return[] = $this->getDaysAgo($maxDays,$ladder);
            $ladder -= 1;
        }
        $return[] = $maxDays;
        $this->daysLadder = $return;
        return $return;
    }


    /**
     *
     * @return bool|void
     */
    private function resolveTimeInterval()
    {
        $now = date('Y-m-d H:i:s');
        //今日凌晨
        $weeHours = date('Y-m-d 00:00:00');
        if ( ! $this->endDate || $this->endDate > $now) {
            $this->endDate = $now;
        }

        if ($this->startDate && $this->endDate) {
            return true;
        }

        $allowSearchTimes = ['today','month','year'];
        if ( ! in_array($this->searchTime, $allowSearchTimes)) {
            $this->searchTime = 'month';
        }

        switch ($this->searchTime) {
            case 'today':
                $this->startDate = $weeHours;
                break;
            case 'month':
                //当月1日凌晨
                $this->startDate = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),1,date('Y')));
                break;
            case 'year':
                //当年1月1日凌晨
                $this->startDate = date('Y-m-d H:i:s',mktime(0,0,0,1,1,date('Y')));
                break;
        }
        return;
    }


}
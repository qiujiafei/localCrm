<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\inventory\models\get;


use commodity\models\AttributeLogicModel;
use commodity\modules\inventory\logics\CommodityBatchLogicModel;
use commodity\modules\inventory\logics\InventoryCommodityLogicModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\inventory\models\get\db\SelectModel;
use common\number\InventoryNumber;
use common\number\NumberDecorator;



class GetModel extends CommonModel
{
    const ACTION_ONE = 'action_one';
    const ACTION_LISTS = 'action_lists';
    const ACTION_NUMBER = 'action_number';
    const ACTION_COMMODITY = 'action_commodity';
    const ACTION_COMMODITIES = 'action_commodities';
    //盘点商品列表
    const ACTION_COMMODITY_LISTS = 'action_inventory_commodity_lists';
    //统计
    const ACTION_STATISTICS = 'action_statistics';
    const ACTION_ALLOW_COMMODITY = 'action_allow_commodity';

    public $token;
    public $id;
    public $status;
    public $pageSize;
    public $page;
    //供应商来源，1为九大爷，2为其他供应商。
    public $origin;
    public $number;
    public $startTime;
    public $endTime;
    public $created_by;
    public $store_id;

    public $depot_id;
    public $commodity_id;
    public $dayDate;
    //搜索用关键字
    public $keyword;
    public $batch_id;

    public function scenarios() {
        return [
            self::ACTION_ONE => ['id'],
            self::ACTION_LISTS => ['page', 'pageSize','status','number','startTime','endTime','created_by'],
            self::ACTION_NUMBER => ['origin'],
            self::ACTION_COMMODITY_LISTS => ['page', 'pageSize','status','number','startTime','endTime','created_by','store_id'],
            self::ACTION_STATISTICS => [],
            self::ACTION_ALLOW_COMMODITY => ['dayDate','depot_id','commodity_id','batch_id','page','pageSize','keyword']
        ];
    }

    public function rules() {
        return [
            [
                ['id'],
                'required',
                'on' => self::ACTION_ONE,
                'message' => 2004,
            ],
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
        ];
    }

    /**
     * 获取一条数据
     * @return array|bool
     */
    public function actionOne()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $data = SelectModel::createOneDataById($this->id);
            if ($storeId != $data['store_id']) {
                throw new \Exception('不可操作',18006);
            }
            return $data;
        } catch (\Exception $e) {
            $this->addError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    /**
     * 获取列表
     * @return array|bool
     */
    public function actionLists()
    {
        $user = AccessTokenAuthentication::getUser();
        try {
            $postData = AttributeLogicModel::getAllowAttributes($this);
            //获取提交的搜索条件
            $where = $this->createWhereByList( AttributeLogicModel::getFieldsOfData($postData,['number','startTime','endTime','created_by']) );

            //限定搜索当前门店数据
            $where[] = ['=','store_id',$user['store_id']];
            $lists = SelectModel::findList($where,$this->pageSize);
            $formatData = $user = [];

            foreach ($lists['lists'] as $key => $model){
                if (null === $model) {
                    continue;
                }
                $formatData = $model->toArray();
                //盈亏状态，负值表示跟仓库应有数据差，正值表示多。
                $formatData['profit_loss'] = $model->getGenerateProfitAndLoss();
                //盘点人
                $user = $model->user->toArray();
                $formatData['user_name'] = $user['name'];
                //盘差金额
                $formatData['diff_price'] = InventoryCommodityLogicModel::getDiffPriceAllByInventoryId($model->id);
                $lists['lists'][$key] = $formatData;
            }
            return $lists;
        } catch (\Exception $e) {
            $this->addError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    /**
     * 盘点单详情列表
     * @return array|bool
     */
    public function actionInventoryCommodityLists()
    {
        $user = AccessTokenAuthentication::getUser();
        try{
            $postData = AttributeLogicModel::getAllowAttributes($this);
            //获取提交的搜索条件
            $where = $this->createWhereByList( AttributeLogicModel::getFieldsOfData($postData,['number','startTime','endTime','created_by']) );
            //限定搜索当前门店数据
            $where[] = ['=','store_id',$user['store_id']];
            //基于inventory条件进行搜索
            $where = $this->addAliasPrefixOfWhere($where,'i');

            $lists = InventoryCommodityLogicModel::findCommodityListForInventory($where,$this->pageSize);
            $formatData = $user = [];
            foreach($lists['lists'] as $key=>$commodityModel) {
                $formatData = $commodityModel->toArray();
                //盘点单号
                $formatData['number'] = $commodityModel->inventory->number;
                //商品名称
                $formatData['commodity_name'] = $commodityModel->commodity->commodity_name;
                //商品规格
                $formatData['specification'] = $commodityModel->commodity->specification;
                //商品条形码
                $formatData['barcode'] = $commodityModel->commodity->barcode;
                //仓库名
                $formatData['depot_name'] = $commodityModel->depot->depot_name;
                //盈亏状态，负值表示跟仓库应有数据差，正值表示多。
                $formatData['profit_loss'] = InventoryCommodityLogicModel::getProfitAndLossByCommodityId($formatData['inventory_id'],$formatData['commodity_id']);
                //盘差金额
                $formatData['diff_price'] = InventoryCommodityLogicModel::getDiffPriceByInventoryIdAndCommodityId($formatData['inventory_id'],$formatData['commodity_id']);
                $lists['lists'][$key] = $formatData;
            }
            return $lists;
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    /**
     * 获取采购单ID
     * @return bool| array
     */
    public function actionNumber()
    {
        try {
            $numberObj = new InventoryNumber(new NumberDecorator($this->origin));
            $data['number'] = $numberObj->getNumber();
            return $data;
        }
        catch (\Exception $e){
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 处理搜索list条件
     * @param array $where
     * @param string $alias 表别名，用于关联查询时使用
     * @return array
     */
    private function createWhereByList(array $where=[],$alias='')
    {
        foreach ($where as $key=>$value){
            if (empty($value)) {
                unset($where[$key]);
            }
        }

        $return = ['and'];
        //没有开始时间，那么直接删除结尾时间
        if (isset($where['endTime']) && !isset($where['startTime'])) {
            unset($where['endTime']);
        }
        if (isset($where['startTime']) && ! isset($where['endTime'])) {
            $where['endTime'] = date('Y-m-d H:i:s');
        }
        if (isset($where['endTime']) && isset($where['startTime'])) {
            $return[] = ['between','created_time',$where['startTime'],$where['endTime']];
            unset($where['startTime'],$where['endTime']);
        }
        //循环数据创建条件
        foreach ($where as $field=>$value) {
            if ( ! is_array($value)) {
                if ($field == 'number') {
                    $return[] = ['like',$field,$value];
                    continue;
                }
                $return[] = ['=',$field,$value];
            }
        }
        return $return;
    }

    /**
     * 对于组合好的where条件添加表前缀
     * @param $where
     * @param string $alias
     * @return mixed
     */
    private function addAliasPrefixOfWhere($where,$alias='')
    {
        foreach ($where as $k => $w)
        {
            if (is_array($w)) {
                $w[1] = $alias.'.'.$w[1];
                $where[$k] = $w;
            }
        }
        return $where;
    }

    /**
     * 统计
     * @return array
     */
    public function actionStatistics()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        //盘盈
        $profit = InventoryCommodityLogicModel::getInventoryProfitLists($storeId);
        //盘亏
        $loss = InventoryCommodityLogicModel::getInventoryLossLists($storeId);

        return [
            'profit' => $profit,
            'loss'   => $loss
        ];
    }

    /**
     * 允许被盘点商品
     */
    public function actionAllowCommodity()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $where = [];

            $where = [
                'and' ,
                ['=','cb.store_id',$storeId],
                //暂时取消库存为0不可盘点的情况，有这个限制是为了防止客户误操作。
                //['!=','cb.stock','0']
            ];
            if($this->commodity_id) {
                array_push($where,['=','cb.commodity_id',$this->commodity_id]);
            }
            if ($this->dayDate) {
                $time = strtotime($this->dayDate);
                $startTime = date('Y-m-d H:i:s',mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time)));
                $endTime = date('Y-m-d H:i:s',mktime(23,59,59,date('m',$time),date('d',$time),date('Y',$time)));
                array_push($where,['between','created_time',$startTime,$endTime]);
            }
            if ($this->depot_id) {
                array_push($where,['=','cb.depot_id',$this->depot_id]);
            }
            if ($this->keyword) {
                array_push($where,['like','c.commodity_name',$this->keyword]);
            }

            //批次ID限制
            if ($this->batch_id) {
                array_push($where,['=','cb.id',$this->batch_id]);
            }

            $cbList = CommodityBatchLogicModel::findListOfAllowCommodity($where,$this->pageSize);
            return $cbList;
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }
}

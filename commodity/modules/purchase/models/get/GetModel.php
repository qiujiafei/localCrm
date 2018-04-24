<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models\get;

use commodity\models\AttributeLogicModel;
use commodity\modules\purchase\models\get\db\CommoditySelectModel;
use commodity\modules\purchase\models\get\db\PurchaseCommoditySelectModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\purchase\models\get\db\SelectModel;
use common\number\NumberDecorator;
use common\number\PurchaseNumber;


class GetModel extends CommonModel
{

    const ACTION_ONE = 'action_one';
    const ACTION_LISTS = 'action_lists';
    const ACTION_NUMBER = 'action_number';
    //上次采购价
    const ACTION_LAST_PRICE = 'action_last_price';
    const ACTION_DETAIL = 'action_detail';
    //允许采购的商品的列表
    const ACTION_ALLOW_COMMODITY = 'action_allow_commodity';

    public $id;
    public $status;
    public $pageSize;
    public $page;
    public $startTime;
    public $endTime;
    public $supplier_id;
    public $keyWord;
    //供应商来源，1为九大爷，2为其他供应商。
    public $origin;

    public $commodityName;

    public function scenarios() {
        return [
            self::ACTION_ONE => ['id'],
            self::ACTION_LISTS => ['page', 'pageSize','startTime','endTime','supplier_id','keyWord'],
            self::ACTION_NUMBER => ['origin'],
            self::ACTION_LAST_PRICE => ['id'],
            self::ACTION_DETAIL => ['page', 'pageSize','startTime','endTime','supplier_id','status','keyWord'],
            self::ACTION_ALLOW_COMMODITY => ['keyWord','page','pageSize']
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
                throw new \Exception('不可操作',12011);
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
            $attributes = AttributeLogicModel::getAllowAttributes($this);
            $where = $this->createWhere(AttributeLogicModel::getFieldsOfData($attributes,[
                'startTime','endTime','supplier_id','status','keyWord'
            ]));
            $where[] = [
                'store_id' => $user['store_id']
            ];
            $selectModel = new SelectModel();
            $lists = $selectModel->findList($where,$this->pageSize);

            $formatData = [];
            $data = [];
            foreach ($lists as $key => $model){
                if (null === $model) {
                    continue;
                }

                $data = $model->toArray();
                //单号
                $formatData['number'] = $data['number'];
                //采购数量，为该采购单采购商品种类，一款商品算一个
                $formatData['quantity'] = count($model->purchaseCommodity) ?? 0;
                //实际支付总额
                $formatData['total_price'] = $model->settlement_price;
                //优惠金额
                $formatData['discount'] = $data['discount'];
                //采购员
                $formatData['purchaseUserName'] = $model->user->name;
                //供应商名称
                $formatData['supplierName'] = $model->supplier->main_name;
                //采购时间
                $formatData['created_time'] = $data['created_time'];
                //采购状态，(0:挂单 1:已结算 2:异常 3:作废)
                $formatData['status'] = $data['status'];
                //备注
                $formatData['comment'] = $data['comment'];

                $lists[$key] = $formatData;
            }

            return [
                'lists' => $lists,
                'count' => $selectModel->getPagination()->pageSize,
                'total_count' => $selectModel->getPagination()->totalCount
            ];
        } catch (\Exception $e) {
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
            $numberObj = new PurchaseNumber(new NumberDecorator($this->origin));
            $data['number'] = $numberObj->getNumber();
            return $data;
        }
        catch (\Exception $e){
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 获取上次采购价格，无果，返回0.00
     * @return array
     */
    public function actionLastPrice()
    {
        $default = [
            'price' => PurchaseCommoditySelectModel::DEFAULT_LAST_PURCHASE_PRICE
        ];
        if ( ! $this->id) {
            return $default;
        }
        $storeId = AccessTokenAuthentication::getUser(true);
        $default['price'] = PurchaseCommoditySelectModel::getLastPriceByCommodityId($this->id,$storeId);
        return $default;
    }

    /**
     * 采购单详情列表
     * @return array|bool
     */
    public function actionDetail()
    {
        try{
            if (isset($supplierId)) {
                unset($supplierId);
            }
            $storeId = AccessTokenAuthentication::getUser(true);
            //获取提交的数据
            $postData = AttributeLogicModel::getAllowAttributes($this);
            //获取搜索条件
            if ($postData['supplier_id']) {
                $supplierId = $postData['supplier_id'];
                unset($postData['supplier_id']);
            }
            //去掉关键字搜索
            if ($postData['keyWord']) {
                $number = $postData['keyWord'];
                unset($postData['keyWord']);
            }

            $where = $this->createWhere(AttributeLogicModel::getFieldsOfData($postData,[
                'startTime','endTime','supplier_id','status','keyWord'
            ]));
            //循环where条件进行赋值
            foreach ($where as $key => $value)
            {
                if (is_array($value)) {
                    $where[$key][1] = 'pc.'.$where[$key][1];
                }
            }

            //替换掉供应商ID搜索条件
            //附加门店ID
            $where[] = [
                'pc.store_id' => $storeId
            ];
            if (isset($supplierId)){
                $where[] = [
                    'p.supplier_id' => $supplierId
                ];
            }
            if (isset($number)) {
                $where[] = ['like','p.number',$number];
            }
//            if ( ! SelectModel::isExistsPurchaseByStoreId($this->id,$storeId)) {
//                throw new \Exception('非当前门店采购单',12011);
//            }

            //获取当前采购单商品列表
            $purchaseCommodityList = PurchaseCommoditySelectModel::findListForDetail($where,$this->pageSize);
            $tempData = $data = [];
            //组合数据
            foreach ($purchaseCommodityList['lists'] as $key=>$model) {
                if (null === $model){
                    continue;
                }
                $tempData = $model->toArray();
                //商品名称
                $tempData['commodity_name'] = $model->commodity->commodity_name;
                //采购单号
                if ($model->purchase) {
                    $tempData['purchase_number'] = $model->purchase->number;
                } else {
                    $tempData['purchase_number'] = '';
                }

                //采购员名称
                if ($model->user){
                    $tempData['username'] = $model->user->account;
                } else {
                    $tempData['username'] = '';
                }

                //商品规格
                $tempData['specification'] = $model->commodity->specification;
                //商品条形码
                $tempData['barcode'] = $model->commodity->barcode;
                //供应商名称
                if ($model->supplier){
                    $tempData['supplier_name'] = $model->supplier->main_name;
                } else {
                    $tempData['supplier_name'] = '';
                }

                //仓库名
                $tempData['depot_name'] = $model->depot->depot_name;

                $data[] = $tempData;
            }
            $purchaseCommodityList['lists'] = $data;
            return $purchaseCommodityList;
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();echo $e->getLine();echo $e->getFile();exit;
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    public function actionAllowCommodity()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $where = ['and'];
        //此为搜索商品时的关键字
        if ($this->keyWord) {
            $where[] = ['like','commodity_name',$this->keyWord];
        }
        array_push($where,['=','store_id',$storeId]);

        $model = new CommoditySelectModel();
        $list = $model->findListByNameOfStore($where,$this->pageSize);
        $tempData = [];
        foreach($list as $key => $m) {
            if (null === $m){
                continue;
            }
            $tempData = $m->toArray();
            $depot = $m->depot;
            $tempData['depot_id'] = '';
            $tempData['depot_name'] = '';
            if ($depot) {
                $tempData['depot_id'] = $depot->id;
                $tempData['depot_name'] = $depot->depot_name;
            }
            $list[$key] = $tempData;
        }
        return [
            'lists' => $list,
            'count' => $model->getPagination()->pageSize,
            'total_count' => $model->getPagination()->totalCount
        ];

    }

    /**
     * 创建where条件，且只支持一维数组
     * @param array $where
     * @return array
     */
    private function createWhere($where = [])
    {
        foreach($where as $key => $value){
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
                //传入关键字，现在只支持采购单号查询
                if ($field == 'keyWord') {
                    $return[] = ['like','number',$value];
                    continue;
                }
                $return[] = ['=',$field,$value];
            }
        }

        return $return;
    }
}

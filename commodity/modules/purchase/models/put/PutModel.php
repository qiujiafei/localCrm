<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\models\put;

use commodity\models\AttributeLogicModel;
use commodity\modules\inventory\logics\CommodityBatchLogicModel;
use commodity\modules\purchase\models\PurchaseCommodityLogicModel;
use commodity\modules\purchase\models\PurchaseLogicModel;
use commodity\modules\purchase\models\put\db\CommodityInsertModel;
use commodity\modules\purchase\models\put\db\FinancePurchaseInsertModel;
use commodity\modules\supplier\models\SupplierLogicModel;
use commodity\modules\supplier\models\SupplierObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\purchase\models\put\db\InsertModel;
use common\number\NumberDecorator;
use common\number\PurchaseNumber;
use Yii;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';

    public $supplier_id;  //供应商ID
    public $store_id;     //门店ID
    public $status;       //状态，标识采购单相关状态
    public $comment;      //备注
    public $purchase_by;  //采购者
    public $origin_price; //原价
    public $discount;     //优惠金额
    public $settlement_price;  //结算价格


    /**
     * 以下属性属于采购单商品详情
     */
    public $commodities;  //采购单中的商品列表

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'supplier_id', 'status', 'comment','commodities','origin_price','discount','settlement_price'
            ],
        ];
    }

    public function rules() {
        return [
            [['supplier_id','origin_price'],'required','message'=>12010],
            ['status', 'in', 'range' => PurchaseLogicModel::PURCHASE_STATUS, 'message' => 12005],
            ['status', 'default', 'value' => PurchaseLogicModel::PURCHASE_STATUS['zero']],
            ['comment','default','value'=> ''],
            ['discount','default','value'=> '0.00'],
            //['settlement_price','filter','filter' => [$this,'filterSettlementPrice']],
            //['discount','filter','filter' => [$this,'filterDiscount']],
        ];
    }

    /**
     * 采购入库，采购商品入库
     * @return array|bool
     * @throws \yii\db\Exception
     */
    public function actionInsert()
    {
        //获取用户信息
        $user = AccessTokenAuthentication::getUser();
        $supplierObject = new SupplierObject();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //获取提交的数据
            $data = AttributeLogicModel::getAllowAttributes($this);

            //格式化数据
            $data = PurchaseLogicModel::resolvePostData($data);

            //计算金额
            $data = PurchaseLogicModel::handleSettlement($data);

            //采购单信息
            $purchases = $data['purchases'];
            //验证供应商合法性
            if( ! $supplierObject->isSupplierOfStore($user['store_id'],$purchases['supplier_id']) ) {
                throw new \Exception('非当前门店供应商',5015);
            }

            $model = new InsertModel();
            //门店ID
            $purchases['store_id'] = $user['store_id'];
            //创建者和最后编辑者
            $purchases['created_by'] = $purchases['last_modified_by'] = $user['id'];
            //采购人默认当前登录用户
            $purchases['purchase_by'] = $user['id'];
            //单号
            $purchases['number'] = $this->createNumber($purchases['supplier_id']);

            //模型赋值并返回
            AttributeLogicModel::setReplaceAttributes($model,$purchases);

            //添加采购单操作
            $resultPurchase = $model->save();
            //采购单商品MODEL
            $commodity = new CommodityInsertModel();
            $commodities = PurchaseCommodityLogicModel::formatAddData($data['commodities'],[
                'purchase_id' => $model->id,
                'store_id' => $user['store_id'],
                'created_by' => $user['id'],
                'last_modified_by' => $user['id']
            ]);

            //过滤非法商品信息
            $commodities = PurchaseCommodityLogicModel::filterAllowsFields($commodities,[
                'commodity_id','depot_id','quantity','unit_id','current_price','last_purchase_price',
                'total_price','purchase_id','store_id','created_by','last_modified_by','comment'
            ]);

            //验证字段合法性并添加入库
            $resultPurchaseCommodity = $commodity->validateFields($commodities);
            //批次存储状态
            $insertBatchResult = true;
            //财务存储状态
            $financeResult = true;
            //status为1时，批次表中增加相关数据，添加财务相关数据
            if ($purchases['status'] == PurchaseLogicModel::PURCHASE_STATUS['one']) {
                //重新赋值
                $data['purchases'] = $purchases;
                //库存存储
                $insertBatchResult = CommodityBatchLogicModel::insertBatchData($data);

                //财务数据格式化
                $financeData = FinancePurchaseInsertModel::createInsertDataOfPurchase($data['commodities'],$purchases);

                //财务表插入
                $financeResult = FinancePurchaseInsertModel::batchInsert($financeData);
            }

            if ($resultPurchase && $resultPurchaseCommodity && $insertBatchResult && $financeResult){
                $transaction->commit();
                return [];
            }
            throw new \Exception('添加失败',12001);
        } catch (\Exception $e) {

            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }


    /**
     * 创建并验证单号合法性
     * @param $supplierId
     * @return string
     * @throws \Exception
     */
    private function createNumber($supplierId)
    {
        $supplierObject = new SupplierObject();
        $origin = $supplierObject->isSupplierOfNineById($supplierId) ? '01' : '02';
        $number = new PurchaseNumber(new NumberDecorator($origin));
        $value = '';
        //确定单号是否重,后期考虑缓存
        while (true) {
            $value = $number->getNumber();
            if ( ! PurchaseLogicModel::isExistsByNumber($value)) {
                break;
            }
        }

        return $value;
    }

}

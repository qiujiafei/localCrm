<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\models\modify;

use commodity\models\AttributeLogicModel;
use commodity\modules\purchase\models\get\db\SelectModel;
use commodity\modules\purchase\models\modify\db\UpdateModel;
use commodity\modules\purchase\models\PurchaseCommodityLogicModel;
use commodity\modules\purchase\models\PurchaseLogicModel;

use commodity\modules\purchase\models\put\db\CommodityInsertModel;
use commodity\modules\supplier\models\SupplierObject;
use common\ActiveRecord\PurchaseCommodityAR;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;
use common\models\Model as CommonModel;
use commodity\modules\supplier\models\SupplierLogicModel;
use yii\db\Exception;
use commodity\modules\inventory\logics\CommodityBatchLogicModel;
use commodity\modules\purchase\models\put\db\FinancePurchaseInsertModel;

class ModifyModel extends CommonModel
{
    //编辑
    const ACTION_EDIT = 'action_edit';
    //作废
    const ACTION_INVALID = 'action_invalid';

    public $id;           //采购单ID，修改时，必传
    public $supplier_id;  //供应商ID
    public $store_id;     //门店ID
    public $status;       //状态，标识采购单相关状态
    public $comment;      //备注
    public $origin_price; //原价
    public $discount;     //优惠金额
    public $settlement_price;  //结算价格

    public $commodities;  //采购单中的商品列表

    public function scenarios() {
        return [
            self::ACTION_EDIT => [
                'id','supplier_id', 'status', 'comment','commodities','origin_price','discount','settlement_price'
            ],
            self::ACTION_INVALID => [
                'id'
            ]
        ];
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules() {
        return [
            [['supplier_id','origin_price','discount'],'required','message'=>12010],
            ['id','required','message'=>12012],
            ['id','filter','filter' => [$this,'checkPurchaseId']],
            ['depot_id', 'integer', 'min' => 1, 'tooBig' => PHP_INT_MAX, 'message' => 12002],
            ['status', 'in', 'range' => PurchaseLogicModel::PURCHASE_STATUS, 'message' => 12005],
            ['status', 'default', 'value' => PurchaseLogicModel::PURCHASE_STATUS['zero']],
            ['comment','default','value'=> ''],
        ];
    }

    /**
     * 只要未结算，均可修改。添加新商品进入采购单，或追删除原有采购单商品，或修改购买数量等
     * @return array|bool
     * @throws \yii\db\Exception
     */
    public function actionEdit()
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

            //采购单原有数据
            $oldPurchase = UpdateModel::findOne($this->id);

            if ($oldPurchase['status'] != PurchaseLogicModel::PURCHASE_STATUS['zero']) {
                throw new \Exception('非挂单状态，不可编辑',12013);
            }

            //验证供应商合法性
            if( ! $supplierObject->isSupplierOfStore($user['store_id'],$oldPurchase['supplier_id']) ) {
                throw new \Exception('非当前门店供应商',5015);
            }
            if ($oldPurchase->store_id != $user['store_id']) {
                throw new \Exception('不可操作其他门店的数据',12011);
            }

            $purchase = $data['purchases'];
            $purchase['last_modified_by'] = $user['id'];
            AttributeLogicModel::setReplaceAttributes($oldPurchase,$purchase);
            $commodityModel = new CommodityInsertModel();

            $commodities = PurchaseCommodityLogicModel::formatAddData($data['commodities'],[
                'purchase_id' => $oldPurchase->id,
                'store_id' => $user['store_id'],
                'created_by' => $user['id'],
                'last_modified_by' => $user['id']
            ]);
            //过滤非法商品信息
            $commodities = PurchaseCommodityLogicModel::filterAllowsFields($commodities,[
                'commodity_id','depot_id','quantity','unit_id','current_price','last_purchase_price',
                'total_price','purchase_id','store_id','created_by','last_modified_by'
            ]);

            //删除原来的商品数据
            $boolDelete = CommodityInsertModel::deleteAll(['purchase_id' => $oldPurchase->id]);

            //验证字段合法性并添加入库
            $resultPurchaseCommodity = $commodityModel->validateFields($commodities);
            //更新值
            $updatePurchase = $oldPurchase->save();

            //批次存储状态
            $insertBatchResult = true;
            //财务存储状态
            $financeResult = true;
            //status为1时，批次表中增加相关数据，添加财务相关数据
            if ($purchase['status'] == PurchaseLogicModel::PURCHASE_STATUS['one']) {
                //重新赋值
                $data['purchases'] = $oldPurchase->toArray();

                //库存存储
                $insertBatchResult = CommodityBatchLogicModel::insertBatchData($data);

                //财务数据格式化
                $financeData = FinancePurchaseInsertModel::createInsertDataOfPurchase($data['commodities'],$data['purchases']);
                //财务表插入
                $financeResult = FinancePurchaseInsertModel::batchInsert($financeData);
            }


            if ($boolDelete && $resultPurchaseCommodity && $updatePurchase && $insertBatchResult && $financeResult) {
                $transaction->commit();
                return [];
            }

            throw new \Exception('操作失败',12014);

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 作废
     * @return array|bool
     * @throws Exception
     */
    public function actionInvalid()
    {
        $user = AccessTokenAuthentication::getUser();
        $transaction = Yii::$app->db->beginTransaction();
        try {

            //采购单原有数据
            $oldPurchases = UpdateModel::findAll(['id'=>$this->id]);
            if (null === $oldPurchases) {
                throw new \Exception('无相关数据',12012);
            }
            //可作废状态
            $purchaseStatusZero = PurchaseLogicModel::PURCHASE_STATUS['zero'];

            foreach ($oldPurchases as $key=>$model) {
                //判断是否可作废
                if ($model->status != $purchaseStatusZero) {
                    throw new \Exception('非挂单状态，不可编辑',12015);
                }
                //是否非法操作其他店铺的数据
                if ($model->store_id != $user['store_id']) {
                    throw new \Exception('不可操作其他门店的数据',12011);
                }
                $oldPurchases[$key] = $model;
            }

            //作废状态
            $purchaseStatusThree = PurchaseLogicModel::PURCHASE_STATUS['three'];
            //采购单状态更新
            $model = new UpdateModel();
            if ($model->updateStatusById(['id'=>$this->id],$purchaseStatusThree) && CommodityInsertModel::deleteAll(['purchase_id' => $this->id])) {
                $transaction->commit();
                return [];
            }
            throw new \Exception('操作失败',12014);
        } catch (\Exception $e)
        {
            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

    /**
     * 回调验证传入采购单ID是否合法
     * @param $id
     * @throws \Exception
     * @return int
     */
    public function checkPurchaseId($id)
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $where = [
            'id' => $id,
            'store_id' => $storeId
        ];
        if ( ! SelectModel::find()->where($where)->exists() ) {
            $this->addError('非当前门店采购单，不可操作',12011);
            return false;
        }

        return $id;
    }
}

<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\inventory\models\put;

use commodity\models\AttributeLogicModel;
use commodity\modules\inventory\logics\CommodityBatchLogicModel;
use commodity\modules\inventory\logics\InventoryLogicModel;
use commodity\modules\inventory\models\put\db\InsertDB;
use commodity\modules\inventory\models\put\db\CommodityInsertModel;
use commodity\modules\supplier\models\SupplierLogicModel;
use commodity\modules\supplier\models\SupplierObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use common\number\InventoryNumber;
use common\number\NumberDecorator;
use Yii;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';

    public $store_id;     //门店ID
    public $status;       //状态，标识采购单相关状态
    public $comment;      //备注
    public $inventory_by; //盘点者，该属性暂时不用，默认使用当前登录者
    public $supplier_id;  //供应商ID
    /**
     * 以下属性属于采购单商品详情
     */
    public $commodities;  //采购单中的商品列表

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'status', 'comment','commodities','supplier_id'
            ],
        ];
    }

    public function rules() {
        return [
            ['depot_id','required','message'=>18003],
            ['commodities','required','message'=>18004],
            ['depot_id', 'integer', 'min' => 1, 'tooBig' => PHP_INT_MAX, 'message' => 12002],
            ['status', 'in', 'range' => InventoryLogicModel::ALLOW_STATUS_VALUE, 'message' => 12005],
            ['status', 'default', 'value' => InventoryLogicModel::ALLOW_STATUS_VALUE['zero']],
            ['comment','default','value'=> ''],
        ];
    }


    public function actionInsert()
    {
        //获取用户信息
        $user = AccessTokenAuthentication::getUser();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //获取提交的数据
            $data = AttributeLogicModel::getAllowAttributes($this);
            unset($data['supplier_id']);

            //格式化数据
            $data = InventoryLogicModel::resolvePostData($data);

            //盘点单信息
            $inventories = $data['inventories'];
            //门店ID
            $inventories['store_id'] = $user['store_id'];
            //当前盘点单录入者
            $inventories['inventory_by'] = $user['id'];

            $inventories['number'] = $this->createInventoryNumber($this->supplier_id);
            //创建者和最后编辑者
            $inventories['created_by'] = $inventories['last_modified_by'] = $user['id'];
            //盘点单模型
            $model = new InsertDB();

            //模型赋值并返回
            AttributeLogicModel::setReplaceAttributes($model,$inventories);

            //添加采购单操作
            $resultInventories = $model->save();

            //采购单商品MODEL
            $commodity = new CommodityInsertModel();
            $commodities = InventoryLogicModel::formatAddData($data['commodities'],[
                'inventory_id' => $model->id,
                'store_id' => $user['store_id'],
                'created_by' => $user['id'],
                'last_modified_by' => $user['id']
            ]);

            //验证字段合法性并添加入库
            $resultInventoryCommodity = $commodity->validateFields($commodities);

            //更新库存
            $updateBatchBool = CommodityBatchLogicModel::updateBatchByInventory($data['commodities']);

            if ($resultInventories && $resultInventoryCommodity && $updateBatchBool){
                $transaction->commit();
                return [];
            }
            throw new \Exception('添加失败',18007);
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
    private function createInventoryNumber($supplierId=0)
    {
        $supplierObject = new SupplierObject();
        $origin = $supplierObject->isSupplierOfNineById($supplierId) ? '01' : '02';
        $number = new InventoryNumber(new NumberDecorator($origin));
        $value = '';
        //确定单号是否重,后期考虑缓存
        while (true) {
            $value = $number->getNumber();
            //同数据库进行重复性检测
            break;
        }

        return $value;
    }
}

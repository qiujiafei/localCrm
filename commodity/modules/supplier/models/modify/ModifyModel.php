<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 15:41
 */
namespace commodity\modules\supplier\models\modify;
use commodity\modules\depot\models\get\db\ExistsModel;
use commodity\modules\supplier\models\SupplierObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\models\AttributeLogicModel;
use commodity\modules\supplier\models\modify\db\UpdateModel;
use commodity\modules\supplier\models\SupplierLogicModel;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ModifyModel extends CommonModel
{
    const ACTION_EDIT = 'action_edit';
    const ACTION_STATUS = 'action_status';

    public $token;
    public $main_name;
    public $id;
    public $store_id;
    public $contact_name;
    public $phone_number;
    public $cell_number;
    public $address;
    public $pay_method;
    public $bank_account_ownner_name;
    public $bank_create_account_bank_name;
    public $bank_card_number;
    public $taxpayer_identification_number;
    public $comment;
    public $created_by;
    public $created_time;
    public $last_modified_by;
    public $last_modified_time;
    public $status;  //供应商状态

    public function rules()
    {
        return [
            [['main_name','contact_name','cell_number'],'required','on'=>self::ACTION_EDIT,'message' => 5001],
            ['main_name','string','length'=>[1,30],'message' => 5002],
            ['contact_name','string','length'=>[1,30],'message' => 5003],
            ['cell_number','match','pattern'=>'/^1[0-9]{10}$/','message' => 5004],
            ['status','in','range' => ['0','1'],'on'=>self::ACTION_STATUS,'message' => 5008]
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_modified_time'],
                ],
                'value' => function(){
                    return new \DateTime('now');
                }
            ]
        ];
    }

    public function scenarios()
    {
        return [
            self::ACTION_EDIT  => ['token','id','main_name',
                'contact_name','cell_number','phone_number','address','pay_method','bank_account_ownner_name',
                'bank_create_account_bank_name','bank_card_number','taxpayer_identification_number','comment'],
            self::ACTION_STATUS => [
                'token','id','status'
            ]
        ];
    }

    /**
     * 编辑更新
     * @return array|bool
     *
     */
    public function actionEdit()
    {
        try {
            $user = AccessTokenAuthentication::getUser();
            $supplierObject = new SupplierObject();
            //获取数据
            $attributes = AttributeLogicModel::getAllowAttributes($this);

            if( ! $user['store_id']){
                throw new \Exception('门店未登录',5005);
            }
            //限定只可修改自主添加的供应商
            if( ! $supplierObject->isExistsSupplierIdById($user['store_id'],$attributes['id'])){
                throw new \Exception('表示该供应商不存在，不可修改',5008);
            }
            //获取数据模型
            $model = UpdateModel::findOne($attributes['id']);

            if (null === $model) {
                throw new \Exception('原始数据不存在，不可编辑',5014);
            }
            //确认是否可编辑，自己添加的可编辑，九大爷数据不可编辑
            if ($supplierObject->isSupplierOfNineById($attributes['id'])) {
                throw new \Exception('九大爷数据，不可编辑',5014);
            }
            //检测该供应商名称是否有被修改，与原来数据相等时，不做修改
            if ($attributes['main_name'] != $model->main_name && $supplierObject->isExistsMainNameByName($user['store_id'],$attributes['main_name'])){
                throw new \Exception('供应商已经存在，不可修改',5006);
            }
            //id不更新
            unset($attributes['id']);
            //$attributes['last_modified_time'] = date('Y-m-d H:i:s');
            $attributes['last_modified_by'] = $user['id'];
            if($model->updateAttributes($attributes)) {
                return [];
            } else {
                throw new \Exception('编辑更新失败',5009);
            }

        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 供应商状态，启用（1表示）or禁用（0表示），
     */
    public function actionStatus()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $supplierObject = new SupplierObject();
            if( ! $storeId ){
                throw new \Exception('门店未登录',5005);
            }
            if ( ! ExistsModel::isDepotOfStoreId($storeId,$this->id) ) {
                throw new \Exception('表示该供应商不属于当前门店，不可修改',5008);
            }

            $model = UpdateModel::findOne($this->id);
            if (null === $model) {
                throw new \Exception('原始数据不存在，不可编辑',5014);
            }
            //确认是否可编辑，自己添加的可编辑，九大爷数据不可编辑
            if ($supplierObject->isSupplierOfNineById($this->id)) {
                throw new \Exception('九大爷数据，不可编辑',5014);
            }
            $attributes = AttributeLogicModel::getAllowAttributes($this);
            //两个值一样，表示不必更新，返回空数组表示更新成功
            if ($attributes['status'] == $model->status) {
                return [];
            }
            //删除id，不可更新
            unset($attributes['id']);

            //开始更新，更新成功返回受影响的条数
            if ($model->updateAttributes($attributes)){
                return [];
            } else {
                throw new \Exception('操作失败',5011);
            }
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }
}
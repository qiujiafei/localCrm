<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 9:56
 */

namespace commodity\modules\supplier\models\put;

use commodity\modules\supplier\models\SupplierObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\models\AttributeLogicModel;
use commodity\modules\supplier\models\SupplierLogicModel;
use commodity\modules\supplier\models\put\db\InsertModel;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';

    public $token;
    public $store_id;
    public $main_name;
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

    public function rules()
    {
        return [
            [['main_name','contact_name','cell_number'],'required','on'=>self::ACTION_INSERT,'message' => 5001],
            ['main_name','string','length'=>[1,30],'message' => 5002],
            ['contact_name','string','length'=>[1,30],'message' => 5003],
            ['cell_number','match','pattern'=>'/^1[0-9]{10}$/','message' => 5004],
            ['phone_number','default','value' => '']
        ];
    }

    public function scenarios()
    {
        return [
            self::ACTION_INSERT => ['token','main_name','contact_name','cell_number','phone_number','address','pay_method','bank_account_ownner_name',
                'bank_create_account_bank_name','bank_card_number','taxpayer_identification_number','comment'
            ]
        ];
    }

    /**
     * @return array|bool
     * 供应商添加
     */
    public function actionInsert()
    {
        try{
            $supplierObject = new SupplierObject();
            //获取数据
            $attributes = AttributeLogicModel::getAllowAttributes($this);

            $user = AccessTokenAuthentication::getUser();
            //获取当前门店ID
            if( ! $user['store_id'])
            {
                throw new \Exception('门店ID不合法',5005);
            }

            //检查当前门店供应商是否唯一
            if( $supplierObject->isExistsMainNameByName($user['store_id'],$attributes['main_name']) ){
                throw new \Exception('该供应商存在',5006);
            }
            //过滤添加九大爷同名供应商
            if ($supplierObject->isExistsMainNameByNine($attributes['main_name'])) {
                throw new \Exception('该供应商存在',5006);
            }
            //存储供应商
            $model = new InsertModel($attributes);
            //预设某些值
            $model->setAttribute('created_by',$user['id']);
            $model->setAttribute('last_modified_by',$user['id']);
            $model->setAttribute('store_id',$user['store_id']);

            if($model->save()){
                return [];
            } else {
                throw new \Exception('添加供应商失败',5007);
            }
        }
        catch (\Exception $e)
        {
            if ($e->getCode() == 0) {
                $this->addError('添加供应商失败',5007);
            }
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }
}
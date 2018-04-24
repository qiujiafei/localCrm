<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 13:31
 */

namespace commodity\modules\supplier\models\get;

use commodity\modules\supplier\models\SupplierObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\models\UserInfoLogic;
use commodity\modules\supplier\models\get\db\SelectModel;
use common\components\handler\ExcelHandler;
use Yii;
use yii\base\UserException;

class GetModel extends CommonModel
{
    const ACTION_LISTS = 'action_lists';
    const ACTION_ONE  = 'action_one';
    const ACTION_DOWN  = 'action_down';

    public $token;
    public $id;
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

    public $page;
    public $pageSize;

    public $names; //供应商名称数组集合，用于数据下载。
    public $searchCategory;  //搜索分类，比如供应商，手机号等
    public $searchKeys;      //搜索关键字，任意字符

    public function scenarios()
    {
        return [
            self::ACTION_LISTS => ['token','page','pageSize','searchCategory','searchKeys','main_name','contact_name','cell_number','phone_number','comment'],
            self::ACTION_ONE  => ['token','id'],
            self::ACTION_DOWN  => ['token','names']
        ];
    }

    /**
     * @return array
     * 首页列表查询，搜索条件暂未完成，对搜索条件有疑问。
     */
    public function actionLists()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        //允许的搜索类别
        $allowCategory = ['main_name','contact_name','cell_number','phone_number','comment'];
        //检测搜索类别合法性，否则使用供应商搜索
        if(!in_array($this->searchCategory,$allowCategory)){
            $this->searchCategory = 'main_name';
        }

        //设定条件
        $where = ['like',$this->searchCategory,$this->searchKeys];

        //若是关键字为空，表示返回全表
        if( ! isset($this->searchKeys[0])){
            $where = [];
        }
        $where['store_id'] = $storeId;

        $selectModel = new SelectModel();
        //newlooc edited
        return $selectModel->findList($where,$this->pageSize);
    }

    /**
     * @return array|bool
     * 编辑信息获取数据
     */
    public function actionOne()
    {
        try{
            $storeId = AccessTokenAuthentication::getUser(true);
            if(!$storeId){
                throw new \Exception('门店未登录',5005);
            }

            $supplierObject = new SupplierObject();
            if ( ! $supplierObject->isSupplierOfStore($storeId,$this->id)) {
                throw new UserException('非当前门店供应商，不可操作',5016);
            }
            $supplierObject->findOneByWhere(['store_id' => $storeId,'id'=>$this->id]);
            return $supplierObject->toArray();
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

    /**
     * @return array|bool
     * 导出数据，暂时导出当前页面和当前选中，要求传递供应商名称
     * 若没有任何供应商名称传递，便导出当前登录门店供应商所有列表
     */
    public function actionDown()
    {
        try{
            $storeId = UserInfoLogic::getStoreId();
            if(!$storeId){
                throw new \Exception('门店未登录',5005);
            }
            //支持get方式传递参数
            if(Yii::$app->request->isGet){
                $names = Yii::$app->request->get('names');
                if (is_string($names) && false !== strpos($names,',')) {
                    $names = explode(',',$names);
                }
                $this->names = $names;
            }

            if(!is_array($this->names)){
                $this->names = (array) $this->names;
            }

            //开始导出功能
            $where  = ['store_id' => $storeId,'main_name' => $this->names];

            if(!isset($this->names[0]) || empty($this->names[0])){
                unset($where['main_name']);
            }

            $fields = ['供应商'=>'main_name','联系人'=>'contact_name','手机'=>'cell_number','联系电话'=>'phone_number',
                '支付方式' => 'pay_method', '银行卡开户人' => 'bank_account_ownner_name','银行卡开户行' => 'bank_create_account_bank_name',
                '银行卡号' => 'bank_card_number', '纳税人识别号' => 'taxpayer_identification_number','状态' => 'status',
                '地址'=>'address','评论'=>'comment','创建者ID'=>'created_by','创建时间'=> 'created_time','修改人ID'=>'last_modified_by',
                '修改时间' => 'last_modified_time'
            ];
            $models = SelectModel::find()->select(array_values($fields))->where($where)->all();
            //组合数据
            $data = [];
            foreach($models as $model)
            {
                $data[] = $model->toArray();
            }
            //excel各列名称
            $title = array_keys($fields);
            //下载文件
            ExcelHandler::output($data,$title,date('Y_m_d'));
            //下载文件方法会停止该脚本，无论对方是否改变，这里进行return确保健壮性
            return [];
        }

        catch(\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }
}

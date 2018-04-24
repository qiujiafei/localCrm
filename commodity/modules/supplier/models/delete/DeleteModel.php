<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 17:27
 */
namespace commodity\modules\supplier\models\delete;
use commodity\modules\purchase\models\PurchaseObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\supplier\models\delete\db\DeleteDBModel;

class DeleteModel extends CommonModel
{
    const ACTION_DELETE = 'action_delete';
    const ACTION_MORE = 'action_more';

    public $token;
    public $names; // 供应商名称，格式为数组，存放被删除供应商原始名称。
    public $pkIds; //供应商主键ID列表

    public function scenarios()
    {
        return [
            self::ACTION_DELETE => ['token','pkIds','names']
        ];

    }

    /**
     * @return array|bool
     * 删除供应商，支持批量删除。
     */
    public function actionDelete()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $purchaseObject = new PurchaseObject();
            if( ! $storeId ){
                throw new \Exception('门店未登录',5005);
            }

            //初始化时，该门店ID必传
            $model = new DeleteDBModel();

            if ( ! $model->checkIsExistsIds($storeId,$this->pkIds)) {
                throw new \Exception('传入被删除id有非法参数，或不属于当前门店供应商',5013);
            }

            //供应商单据判断状态，因为所有单据均需要通过采购方可产生，因此，只需要判断该门店是否有在该供应商采购即可。
            if($purchaseObject->isHaveBillOfPurchase($storeId,$model->getIds())){
                throw new \Exception('某个供应商有单据，不可删除，提醒首条不允许删除的数据',5012);
            }

            $where = ['store_id' => $storeId,'id'=> $model->getIds()];

            if(DeleteDBModel::deleteAll($where)){
                return [];
            } else {
                throw new \Exception('删除失败',5011);
            }

        }
        catch(\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

}
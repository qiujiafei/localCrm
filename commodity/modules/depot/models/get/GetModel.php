<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:20
 */

namespace commodity\modules\depot\models\get;
use commodity\modules\depot\models\DepotObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\depot\models\get\db\SelectModel;

/**
 * Class GetModel
 * @package commodity\modules\depot\models\get
 */
class GetModel extends CommonModel
{
    const ACTION_LISTS = 'action_lists';
    const ACTION_ONE  = 'action_one';

    public $token;
    public $depot;
    public $page;
    public $pageSize;


    public function rules()
    {
        return [
            ['depot','filter','filter'=>'trim']
        ];
    }

    public function scenarios()
    {
        return [
            self::ACTION_LISTS => [
                'token','page','pageSize'
            ],
            self::ACTION_ONE => [
                'token','depot'
            ]
        ];
    }

    /**
     * @return array
     * @throws \Exception
     * 门店列表，暂时未做搜索
     */
    public function actionLists()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $model = new SelectModel();
            //目前该查询不支持总店的情况，只支持当前门店。
            $where = ['store_id' => $storeId];
            return $model->findList($where,$this->pageSize);
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

    /**
     * @return array
     * @throws \Exception
     * 获取信息，无果返回空数组
     */
    public function actionOne()
    {
        try {
            $storeId = AccessTokenAuthentication::getUser(true);
            $depotObject = new DepotObject();
            $depotObject->getDepotOfStoreByIdOrName($storeId,$this->depot);
            return $depotObject->toArray();
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }
}
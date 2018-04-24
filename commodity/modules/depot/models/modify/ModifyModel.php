<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 17:03
 */

namespace commodity\modules\depot\models\modify;
use commodity\modules\depot\models\DepotObject;
use common\models\Model as CommonModel;
use commodity\models\AttributeLogicModel;
use commodity\modules\depot\models\modify\db\UpdateModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class ModifyModel extends CommonModel
{
    const ACTION_EDIT = 'action_edit';

    public $id;  //仓库的id
    public $token;
    public $depot_name;
    public $comment;
    public $status;

    public function rules()
    {
        return [
            [ ['depot_name'],'required','on'=>self::ACTION_EDIT,'message'=>8001 ]
        ];
    }

    public function scenarios()
    {
        return [
            self::ACTION_EDIT => [
                'token','id','depot_name','comment'
            ]
        ];
    }

    public function actionEdit()
    {
        try
        {
            $user = AccessTokenAuthentication::getUser();
            $storeId = $user['store_id'];
            $attributes = AttributeLogicModel::getAllowAttributes($this);
            $depotObject = new DepotObject();
            //检测当前门店仓库可编辑性
            if ( ! $depotObject->isExistsDepotIdOfStore($storeId,$attributes['id']) ){
                throw new \Exception('仓库不存在',8004);
            }

            $model = UpdateModel::findOne($attributes['id']);

            if (null === $model){
                throw new \Exception('查询不到该数据，表示非法',8004);
            }
            //仓库名称预处理
            if ($model->depot_name != $attributes['depot_name']){
                //检测当前仓库是否被使用
                if ( $depotObject->isExistsDepotNameOfStore($storeId,$attributes['depot_name']) ) {
                    throw new \Exception('仓库已存在',8003);
                }
                //$model->isUpdateDepotName = false;
                //$model->on(UpdateModel::EVENT_UPDATE_COMMODITY_DEPOT_NAME,[$model,'eventUpdateDepotName'],['old_depot_name' => $model->depot_name]);
            }
            //更新操作者
            $attributes['last_modified_by'] = $user['id'];
            //更新字段的值
            $model->changeAttributes($attributes,['id']);
            //更新数据库
            $model->updateDepotData();
            //通过检测错误信息判定是否更新成功
            if ($model->errors)
            {
                $errors = $model->getFirstErrors();
                $errorCode = current($errors);
                throw new \Exception('更新出错',$errorCode);
            }

            return [];
        }
        catch (\Exception $e)
        {
            //表示有重复的复合主键值将要被存储，故抛出错误
            if ($e->getCode() == 23000){
                $this->addError($e->getMessage(),8003);
                return false;
            }
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }



}

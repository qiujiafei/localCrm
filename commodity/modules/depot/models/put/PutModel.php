<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:26
 */
namespace commodity\modules\depot\models\put;
use commodity\modules\depot\models\DepotObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\models\AttributeLogicModel;
use commodity\modules\depot\models\get\db\ExistsModel;
use commodity\modules\depot\models\put\db\InsertModel;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';

    public $token;
    public $depot_name;
    public $store_id;
    public $comment;
    public $status;

    public function scenarios()
    {
        return [
            self::ACTION_INSERT => ['token','depot_name','comment'],
        ];
    }

    public function rules()
    {
        return [
            [['token','depot_name'],'required','on'=>self::ACTION_INSERT,'message'=>8001],
            ['comment','default','value'=>'','on'=>self::ACTION_INSERT],
        ];
    }

    public function actionInsert()
    {
        try
        {
            $attributes = AttributeLogicModel::getAllowAttributes($this);
            $depotObject = new DepotObject();
            $user = AccessTokenAuthentication::getUser();

            if ( $depotObject->isExistsDepotNameOfStore($user['store_id'] , $attributes['depot_name']) ) {
                throw new \Exception('仓库已存在',8003);
            }

            $model = new InsertModel($attributes);

            //设定创建者ID，添加的时候同编辑者
            $model->setAttribute('created_by',$user['id']);
            $model->setAttribute('last_modified_by',$user['id']);
            $model->setAttribute('store_id',$user['store_id']);

            if ($model->save()){
                return [];
            } else {
                throw new \Exception('存储失败',8002);
            }
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
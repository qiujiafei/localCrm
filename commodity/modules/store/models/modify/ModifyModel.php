<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 17:03
 */

namespace commodity\modules\store\models\modify;
use commodity\models\AttributeLogicModel;
use commodity\modules\store\models\modify\db\UpdateModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;

/**
 * Class ModifyModel
 * @description 更新
 * @package commodity\modules\store\models\modify
 */
class ModifyModel extends CommonModel
{
    //编辑
    const ACTION_EDIT = 'action_edit';

    public $token;
    public $name;
    public $en_name;
    public $parent_id;
    public $address;
    public $phone_number;
    public $location;
    public $is_main_store;
    public $status;
    public $comment;
    public $created_time;
    public $created_by;
    public $last_modified_time;
    public $last_modified_by;

    public function rules()
    {
        return [
            [['name','en_name','address','phone_number'],'required','on'=>self::ACTION_EDIT,'message' => 11002],
            ['phone_number','match','pattern'=>'/^1[0-9]{10}$/','message' => 11003],
            ['parent_id','default','value'=>-1],
            ['comment','default','value'=>''],
            ['is_main_store','default','value'=>0],
            ['location','default','value'=>''],
            ['status','default','value'=>1],
        ];
    }

    public function scenarios()
    {
        return [
            self::ACTION_EDIT => ['token','name','en_name','parent_id','address',
                'phone_number','location','is_main_store','status','comment'
                ]
        ];
    }

    /**
     * 编辑和信息补全
     * @return array
     */
    public function actionEdit()
    {
        try
        {
            $user = AccessTokenAuthentication::getUser();
            $storeId = $user['store_id'];
            $postData = AttributeLogicModel::getAllowAttributes($this);
            $postData['last_modified_by'] = $user['id'];
            $model = UpdateModel::findOne(['id'=>$storeId]);
            //更新属性
            AttributeLogicModel::setReplaceAttributes($model,$postData);

            if($model->save()) {
                return [];
            }
            throw new \Exception('更新失败或者未更新',11004);
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }

}

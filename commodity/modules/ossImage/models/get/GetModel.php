<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\ossImage\models\get;

use common\models\Model as CommonModel;
use commodity\models\common\OSSUploadConfigForCommodity;
use common\components\tokenAuthentication\AccessTokenAuthentication as User;
use Yii;

class GetModel extends CommonModel
{

    const ACTION_OSS_PERMISSION = 'action_permission';

    public $file_suffix;
    
    public function scenarios()
    {
        return [
            self::ACTION_OSS_PERMISSION => ['file_suffix'],
        ];
    }

    public function rules()
    {
        return [
            [
                ['file_suffix'],
                'required',
                'message' => 1,
            ],
        ];
    }
    
    public function actionPermission()
    {
        $uploadConfig = new OSSUploadConfigForCommodity([
            'userId' => User::getUser()->id,
            'fileSuffix' => $this->file_suffix,
        ]);
        if ($permission = $uploadConfig->getPermission())
        {
            return $permission;
        }
        $this->addError('getOssPermission', 5093);
        return false;
    }
}

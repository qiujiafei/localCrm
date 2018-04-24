<?php

namespace commodity\modules\store\models\put;

use common\models\Model as CommonModel;

/**
 *
 * Class PutModel
 * @description 门店添加，由于数据来源于九大爷平台，这里暂时不做相关任何逻辑处理
 * @author hejinsong@9daye.com.cn
 * @package commodity\modules\store\models\put
 */
class PutModel extends CommonModel
{
    public function scenarios()
    {
        return [];
    }

    public function rules()
    {
        return [];
    }
}
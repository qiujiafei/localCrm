<?php

/**
 * CRM system for 9daye
 *  盘点模块
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\inventory;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'commodity\modules\inventory\controllers';
        Yii::$app->getUrlManager()->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

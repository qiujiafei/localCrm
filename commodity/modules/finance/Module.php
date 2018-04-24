<?php

/**
 * CRM system for 9daye
 * 财务模块
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\finance;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'commodity\modules\finance\controllers';
        Yii::$app->urlManager->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

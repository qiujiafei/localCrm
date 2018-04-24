<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'commodity\modules\purchase\controllers';
        Yii::$app->urlManager->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

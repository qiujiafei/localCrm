<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wj <wangjie@9daye.com.cn>
 */

namespace commodity\modules\memberPoint;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'commodity\modules\memberPoint\controllers';
        Yii::$app->getUrlManager()->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

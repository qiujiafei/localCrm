<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'commodity\modules\customercarstirebrand\controllers';
        Yii::$app->urlManager->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

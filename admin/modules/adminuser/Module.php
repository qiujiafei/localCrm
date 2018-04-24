<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser;

use Yii;
use yii\base\Module as YiiBaseModule;

class Module extends YiiBaseModule
{
    public $defaultRoute = 'index';
    
    public function init()
    {
        $this->controllerNamespace = 'admin\modules\adminuser\controllers';
        Yii::$app->getUrlManager()->addRules([
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>' => $this->id . '/<controller>/<action>',
            $this->id . '/<controller:[a-z-]+>/<action:[a-z-]+>.do' => $this->id . '/<controller>/<action>',
        ]);
    }
    
}

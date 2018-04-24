<?php

namespace commodity\modules\authentication\models;

use common\components\ApiVisitor;
use common\ActiveRecord\EmployeeUserAR;
use common\ActiveRecord\StoreAR;
use common\components\rbac\services\RoleInvokableFactory;
use Yii;

class JiuDaYeAuth
{
    public function isValid(string $account, string $passwd)
    {
        $url = $this->getUrl();

        if($url === null) {
            return false; 
        }

        if(strlen($account) === 9) {
            $data = [
                'account' => $account,
                'passwd' => $passwd
            ];
        } else {
            $data =  [
                'mobile' => $account,
                'passwd' => $passwd
            ];
        }

        $result = (new ApiVisitor())(
            $url,
            $data,
            ApiVisitor::METHOD_POST
        );

        if(! ($result = $this->parserResult($result))) {
            return false;
        }

        return $result;

    }

    public function registerUser(string $username, string $password, array $result)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $date = date('Y-m-d H:i:s');
            if(! $user = EmployeeUserAR::findOne(['account' => $result['account']])) {    
                $user = new EmployeeUserAR();
                $user->status       = 1;
                $user->store_id     = $this->insertStore($username, $result['mobile']);
                $user->created_by   = -1;
                $user->created_time = $date;
            }
            $user->account      = $result['account'];
            $user->passwd       = password_hash($password, PASSWORD_DEFAULT);
            $user->mobile       = $result['mobile'] ?? '';
            $user->name         = '九大爷' . $result['account'];
            $user->email        = $this->email ?? '';
            $user->orignate     = 1;
            $user->last_modified_time = $date;
            $res = $user->save();
            $this->setRole($user->id);
            $transaction->commit();
        } catch(\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        return $res;
    }

    private function setRole(int $userId)
    {
        $roleHandler = (new RoleInvokableFactory)($userId);
        return $roleHandler->changeRole('admin');
    }

    private function checkExists($username) 
    {
        return EmployeeUserAR::find()->where(['account' => $username, 'orignate' => 1])->exists();
    }

    private function insertStore($username, $mobile)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $date = date('Y-m-d H:i:s');
            $store = new StoreAR();
            $store->parent_id = -1;
            $store->phone_number = $mobile;
            $store->created_time = $date;
            $store->last_modified_time = $date;
            $store->created_by = -1;
            $store->last_modified_by = -1;
            $store->comment = sprintf('来自九大爷平台,用户%s的门店.', $username);
            $res = $store->save();
            $transaction->commit();
        } catch(\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        return $store->id;
    }

    private function parserResult(array $result)
    {
        if(! isset($result['body'])) {
            return false;
        }

        $result = json_decode($result['body'], true);

        if((int)$result['status'] === 200) {
            return $result['data'];
        }

        return false;
    }

    private function getUrl()
    {
        $params = Yii::$app->params;

        if(isset($params['JiuDaYeAuthAPI'])) {
            return $params['JiuDaYeAuthAPI'];
        }
        return null;
    }
}

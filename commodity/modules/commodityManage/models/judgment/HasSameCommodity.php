<?php

namespace commodity\modules\commodityManage\models\judgment;

use common\components\tokenAuthentication\AccessTokenAuthentication as User;
use commodity\modules\commodityManage\models\get\db\Select;
use common\exceptions\Exception;

class HasSameCommodity
{
    private $commodity;

    public function __construct(array $data)
    {
        if(!isset($data['commodity_name'], $data['barcode'])) {
            throw new Exception('Commodity information need commodity_name and barcode to find specific one.');
        }
        $this->commodity = $data;
    }

    public function __invoke()
    {
        return Select::find()
            ->where([
                'commodity_name' => $this->commodity['commodity_name'],
                'barcode' => $this->commodity['barcode'],
                'store_id' => User::getUser(true)
            ])->exists();
    }
}

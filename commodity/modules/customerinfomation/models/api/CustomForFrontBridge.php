<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\api;

use commodity\modules\frontBridge\models\interfaces\CustomInterface; 
use commodity\modules\bill\models\get\GetModel as BillCustom;
use commodity\modules\customerinfomation\models\get\GetModel as Custom;

/**
 * Bridge for front end API of Custom infomation.
 */
class CustomForFrontBridge implements CustomInterface
{
    /**
     * Custom count from bill.
     *
     * @return array
     */
    public function customFromBill()
    {
        $billCustom = new BillCustom([
            'scenario' => BillCustom::ACTION_CUSTOMER,
            'attributes' => [],
        ]);

        return $billCustom->process();
    }

    /**
     * Member count
     *
     * @return number
     */
    public function memberCount()
    {
        $custom = new Custom([
            'scenario' => Custom::ACTION_GETMEMBERSTATISTICS,
            'attributes' => [],
        ]);

        return $custom->process();

    }

    /**
     * Member increse of this month
     *
     * @return number
     */
    public function monthMemberIncrese()
    {
        $custom = new Custom([
            'scenario' => Custom::ACTION_GETMEMBERSTATISTICS,
            'attributes' => [
                'type' => 2,
            ],
        ]);

        return $custom->process();

    }

}

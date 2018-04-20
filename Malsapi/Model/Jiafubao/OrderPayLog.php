<?php

class Model_Jiafubao_OrderPayLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'jfb_order_pay_log';
    }
}

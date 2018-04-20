<?php

class Model_Jiafubao_OrderItem extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'jfb_order_items';
    }
}

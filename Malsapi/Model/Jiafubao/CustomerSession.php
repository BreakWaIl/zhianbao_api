<?php

class Model_Jiafubao_CustomerSession extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'jfb_customer_session';
    }
}

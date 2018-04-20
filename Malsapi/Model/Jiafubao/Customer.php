<?php

class Model_Jiafubao_Customer extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'jfb_customer';
    }
}

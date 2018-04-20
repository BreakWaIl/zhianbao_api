<?php

class Model_Building_BillProject extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_bill_project';
    }
}

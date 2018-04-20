<?php

class Model_Building_BillStaff extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_bill_staff';
    }
}

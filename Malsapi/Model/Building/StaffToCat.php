<?php

class Model_Building_StaffToCat extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_staff_to_cat';
    }
}

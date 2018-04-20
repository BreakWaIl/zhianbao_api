<?php

class Model_Building_Staff extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_staff';
    }
}

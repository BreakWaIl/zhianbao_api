<?php

class Model_Building_UserRole extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_user_role';
    }
}

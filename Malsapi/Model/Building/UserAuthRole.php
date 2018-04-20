<?php

class Model_Building_UserAuthRole extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_user_auth_role';
    }
}

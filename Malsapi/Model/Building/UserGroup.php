<?php

class Model_Building_UserGroup extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_user_group';
    }
}

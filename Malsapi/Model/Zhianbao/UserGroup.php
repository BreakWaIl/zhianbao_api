<?php

class Model_Zhianbao_UserGroup extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_user_group';
    }
}

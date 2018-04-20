<?php

class Model_Zhianbao_User extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_user';
    }
}

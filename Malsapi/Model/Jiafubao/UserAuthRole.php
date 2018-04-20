<?php

class Model_Jiafubao_UserAuthRole extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'jfb_user_auth_role';
    }
}

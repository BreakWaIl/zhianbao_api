<?php

class Model_Zhianbao_UserLoginLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_user_login_log';
    }
}

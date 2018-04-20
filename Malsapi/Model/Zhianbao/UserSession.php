<?php

class Model_Zhianbao_UserSession extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_user_session';
    }
}

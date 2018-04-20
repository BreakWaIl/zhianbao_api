<?php

class Model_Building_SignLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_sign_log';
    }
}

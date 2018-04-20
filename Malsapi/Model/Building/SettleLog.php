<?php

class Model_Building_SettleLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_settle_log';
    }
}

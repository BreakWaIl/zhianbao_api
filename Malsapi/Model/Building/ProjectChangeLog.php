<?php

class Model_Building_ProjectChangeLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_project_change_log';
    }
}

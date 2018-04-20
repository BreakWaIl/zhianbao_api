<?php

class Model_Building_Construction extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_construction_log';
    }
}

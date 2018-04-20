<?php

class Model_Building_Cat extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_cat';
    }
}

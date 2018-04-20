<?php

class Model_Building_Project extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_project';
    }
}

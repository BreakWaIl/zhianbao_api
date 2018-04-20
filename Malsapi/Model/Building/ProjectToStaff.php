<?php

class Model_Building_ProjectToStaff extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_project_to_staff';
    }
}

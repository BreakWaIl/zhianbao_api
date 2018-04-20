<?php

class Model_Building_ProjectToCat extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_project_to_cat';
    }
}

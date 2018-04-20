<?php

class Model_Building_ProjectStatistics extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_project_statistics';
    }
}

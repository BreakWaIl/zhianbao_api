<?php

class Model_Zhianbao_LicenseType extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_regulator_license';
    }
}

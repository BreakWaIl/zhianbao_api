<?php

class Model_Zhianbao_RegulatorToCustomer extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_regulator_to_company';
    }
}

<?php

class Model_Building_Visa extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'building_contract_visa';
    }
}

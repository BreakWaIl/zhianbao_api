<?php

class Model_Zhianbao_Organization extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_company_organization';
    }
}

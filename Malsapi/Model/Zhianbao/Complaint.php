<?php

class Model_Zhianbao_Complaint extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_complaint';
    }
}

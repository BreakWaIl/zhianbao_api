<?php

class Model_Zhianbao_SafeApply extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_safe_self_apply';
    }
}

<?php

class Model_Zhianbao_SafeApplyLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_safe_self_apply_log';
    }
}

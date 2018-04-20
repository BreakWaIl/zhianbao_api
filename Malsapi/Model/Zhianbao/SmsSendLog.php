<?php

class Model_Zhianbao_SmsSendLog extends PhalApi_Model_NotORM {

    protected function getTableName($id) {
        return 'zab_sms_send_log';
    }
}

<?php

class Model_Zhianbao_WechatAuth extends PhalApi_Model_NotORM {

    

    protected function getTableName($id) {
        return 'zab_wechat_auth';
    }
}

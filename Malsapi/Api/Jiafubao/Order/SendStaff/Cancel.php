<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_SendStaff_Cancel extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家服云公司ID'),
                'sendId' => array('name' => 'send_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '推荐ID'),
                'type' => array('name' => 'type', 'type' => 'enum','range' => array('once','all'), 'require' => true, 'desc' => '撤销类型 once:仅撤回该订单的推荐 all:撤销对于该公司'),
            ),
        );
    }


    /**
     * 撤销推荐家政员
     * #desc 用于撤销推荐家政员
     * #return int code 操作码，0表示成功
     * #return int status  状态
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $companyId = $this->jfbCompanyId;
        $sendId = $this->sendId;
        $type = $this->type;
        $sendStaffDomain = new Domain_Jiafubao_SendStaff();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $status = $sendStaffDomain->cancelSendStaff($sendId,$companyId,$type);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}

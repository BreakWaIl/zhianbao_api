<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Settle_Process extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'settleId' => array('name' => 'settle_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '账单ID'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  /**
     * 用于完成结算单
     * #desc 用于用于完成结算单
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断结算单是否存在
        $billSettleDomain = new Domain_Building_BillSettle();
        $settleInfo = $billSettleDomain->getBaseInfo($this->settleId);
        if (empty($settleInfo)) {
            $rs['code'] = 206;
            $rs['msg'] = T('Bill not exists');
            return $rs;
        }
        //判断结算单状态
        if($settleInfo['settle_status'] == 'y'){
            $rs['code'] = 210;
            $rs['msg'] = T('Bill finish');
            return $rs;
        }
        $data = array();
        $data['settle_id'] = $this->settleId;
        $data['settle_status'] = 'y';
        $data['last_modify'] = time();
        $data['operate_id'] = $this->operateId;
        $info = $billSettleDomain->process($data,$settleInfo);

//        try {
//            DI ()->notorm->beginTransaction ( 'db_api' );
//            $info = $billSettleDomain->process($data,$settleInfo);
//            DI ()->notorm->commit( 'db_api' );
//
//        } catch ( Exception $e ) {
//
//            DI ()->notorm->rollback ( 'db_api' );
//            $rs ['code'] = $e->getCode ();
//            $rs ['msg'] = $e->getMessage ();
//        }
        if( $info){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}

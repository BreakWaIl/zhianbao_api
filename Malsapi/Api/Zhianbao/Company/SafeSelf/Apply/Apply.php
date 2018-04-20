<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeSelf_Apply_Apply extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'userId' => array('name' => 'user_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '用户ID'),
                     'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '申报ID'),
            ),
		);
 	}

  
  /**
     * 提交安全生产申报
     * #desc 用于提交安全生产申报
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

        //判断用户是否存在
        $domainUser = new Domain_Zhianbao_User();
        $userInfo = $domainUser->getBaseInfo($this->userId);
        if (empty($userInfo)) {
            DI()->logger->debug('Company not exists', $this->userId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断申请是否存在
        $applyDomain = new Domain_Zhianbao_SafeApply();
        $applyInfo = $applyDomain->getBaseInfo($this->applyId);
        if(! $applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        if($applyInfo['status'] != 'wait'){
            DI()->logger->debug('Apply failed', $this->applyId);

            $rs['code'] = 116;
            $rs['msg'] = T('Apply failed');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyDomain = new Domain_Zhianbao_SafeApply();
            $applyId = $applyDomain->submitApply($this->companyId,$this->applyId,$userInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['info'] = $applyId;
        return $rs;
    }

}


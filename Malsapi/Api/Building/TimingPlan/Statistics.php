<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_TimingPlan_Statistics extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
		);
 	}

  /**
     * 统计当前公司下项目人员信息
     * #desc 用于统计当前公司下项目人员信息
     * #return int code 操作码，0表示成功
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
        if($companyInfo['type'] != 'building'){
            $rs['code'] = 194;
            $rs['msg'] = T('Api No permissions');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $statisticsDomain = new Domain_Building_Statistics();
            $statisticsDomain->statistics($this->companyId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }
	
}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeSelf_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '申请ID'),
            ),
		);
 	}

  
  /**
     * 获取申报安全生产申报详情
     * #desc 用于获取申报安全生产申报详情
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

        //判断申请是否存在
        $applyDomain = new Domain_Zhianbao_SafeApply();
        $applyInfo = $applyDomain->getBaseInfo($this->applyId);
        if(! $applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }

        $rs['info'] = $applyInfo;
        return $rs;
    }

}


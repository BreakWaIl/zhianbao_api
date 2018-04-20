<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeTemplate_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
            ),
		);
 	}
  
  /**
   * 获取生产安全标准化模板列表
   * #desc 用于获取生产安全标准化模板列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if(empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //获取公司所属监管者模板
        $templateDomain = new Domain_Zhianbao_SafeTemplate();
        $filter = array('company_id' => $this->companyId);
        $list = $templateDomain->getTemplate($filter);

        $rs['list'] = $list;
        return $rs;
    }
	
}

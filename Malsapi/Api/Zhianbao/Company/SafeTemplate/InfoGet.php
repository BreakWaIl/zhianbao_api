<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeTemplate_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'templateId' => array('name' => 'template_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '模板ID'),
            ),
		);
 	}

  
  /**
     * 获取生产安全标准化模板详情
     * #desc 用于获取生产安全标准化模板详情
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

        //判断模板是否存在
        $templateDomain = new Domain_Zhianbao_SafeTemplate();
        $templateInfo = $templateDomain->getBaseInfo($this->templateId);
        if(! $templateInfo){
            DI()->logger->debug('Template not found', $this->templateId);

            $rs['code'] = 113;
            $rs['msg'] = T('Template not exists');
            return $rs;
        }

        $rs['info'] = $templateInfo;
        return $rs;
    }

}


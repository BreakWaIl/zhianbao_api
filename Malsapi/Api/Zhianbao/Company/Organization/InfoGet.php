<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Organization_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'layoutId' => array('name' => 'layout_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '结构ID'),
            ),
        );
    }
  
  /**
     * 获取安全组织结构
     * #desc 用于获取安全组织结构
     * #return int code 操作码，0表示成功
     * #return int company_id 公司ID
     * #return string name 名称
     * #return string content 安全组织结构
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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

        //判断组织结构是否存在
        $organizationDomain = new Domain_Zhianbao_Organization();
        $info = $organizationDomain->getBaseInfo($this->layoutId);
        if( !$info) {
            DI()->logger->debug('Security organization structure not exist', $this->layoutId);

            $rs['code'] = 145;
            $rs['msg'] = T('Security organization structure not exist');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}

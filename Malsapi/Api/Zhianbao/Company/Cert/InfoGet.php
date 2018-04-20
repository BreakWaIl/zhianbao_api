<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Cert_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'safeId' => array('name' => 'safe_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '人员ID'),
            ),
        );
    }
  
  /**
     * 获取人员证件详情
     * #desc 用于获取人员证件详情
     * #return int code 操作码，0表示成功
     * #return int company_id 公司ID
     * #return int staff_id 员工ID
     * #return string name 员工名称
     * #return string img_url 图片路径
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

        //判断人员证件是否存在
        $certDomain = new Domain_Zhianbao_Cert();
        $info = $certDomain->getBaseInfo($this->safeId);
        if( !$info) {
            DI()->logger->debug('Safe cert not found', $this->safeId);

            $rs['code'] = 115;
            $rs['msg'] = T('Safe cert not exists');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}

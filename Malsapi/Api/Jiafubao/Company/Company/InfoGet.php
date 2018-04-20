<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Company_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
        );
    }
  
  /**
     * 获取公司基本信息信息详情
     * #desc 用于获取公司基本信息详情
     * #return int code 操作码，0表示成功
     * #return int id 保险记录ID
     * #return int company_id 公司ID
     * #return string name 公司名称
     * #return string address 注册地址
     * #return string legal_person 法人代表
     * #return string register_capital 注册资金
     * #return string company_type 公司类型
     * #return string register_department 登记机关
     * #return int register_time 成立时间
     * #return string organization_code 组织机构代码证号
     * #return string telephone 联系电话
     * #return string zip_code 邮编
     * #return string business_scope 经营范围
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
        //获取公司信息
        $companyDomain = new Domain_Jiafubao_Company();
        $recordInfo = $companyDomain->getBaseInfo($this->companyId);

        $rs['info'] = $recordInfo;

        return $rs;
    }
    
}

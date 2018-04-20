<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Company_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
        );
    }
  
  /**
     * 获取公司基本信息详情
     * #desc 用于获取公司基本信息详情
     * #return int code 操作码，0表示成功
     * #return array company_info 注册信息
     * #return array basic_info 基本信息
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
        if(!in_array($this->companyId, $companyIds)){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        //获取公司注册信息
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }else{
            $companyInfo['create_time'] = date("Y-m-d H:i:s",$companyInfo['create_time']);
            $companyInfo['last_modify'] = date("Y-m-d H:i:s",$companyInfo['last_modify']);
        }
        //获取公司基本信息
        $companyDomain = new Domain_Jiafubao_Company();
        $BasicInfo = $companyDomain->getBaseInfo($this->companyId);

        $rs['info']['company_info'] = $companyInfo;
        $rs['info']['basic_info'] = $BasicInfo;

        return $rs;
    }
    
}

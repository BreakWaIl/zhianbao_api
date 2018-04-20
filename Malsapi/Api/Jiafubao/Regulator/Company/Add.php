<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Company_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id', 'type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '监管者ID'),
                'companyId' => array('name' => 'company_id', 'type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '公司ID'),
            ),
        );
    }

    /**
     * 获取添加监管者下属公司
     * #desc 用于获取添加监管者下属公司
     * #return int code 操作码，0表示成功
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
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $userDomain = new Domain_Jiafubao_User();
        $info = $userDomain->addReguToCompany($this->regulatorId,$this->companyId);
        if( !$info){
            $rs['code'] = 102;
            $rs['msg'] = T('Add failed');
            return $rs;
        }
        $rs['status'] = $info;

        return $rs;
    }

}

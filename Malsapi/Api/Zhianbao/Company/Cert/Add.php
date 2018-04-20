<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Cert_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                'certInfo' => array('name' => 'cert_info', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '证书信息:array(array(type_id,array(id,url)),array(type_id,array(id,url))'),
            ),
        );
    }


    /**
     * 添加员工证件
     * #desc 用于添加员工证件
     * #return int code 操作码，0表示成功
     * #return int cert_id  证件ID
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
        //判断员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'name' => $staffInfo['name'],
            'certInfo' => $this->certInfo,
        );
        $certDomain = new Domain_Zhianbao_Cert();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $certId = $certDomain->addCert($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['cert_id'] = $certId;

        return $rs;
    }

}

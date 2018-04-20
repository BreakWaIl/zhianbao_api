<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Skill_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'type' => array('name' => 'type', 'type' => 'enum', 'range' => array('society','government'), 'default' => 'society', 'require' => true, 'desc' => '证书类型:society 协会  government 政府'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '证书图片:array(array(id,url)),array(id,url)'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '技能名称'),
                'level' => array('name' => 'level', 'type' => 'enum', 'range' => array('1','2','3','4'), 'default' => '1', 'require' => true, 'desc' => '证书等级: 1 专项级 2 初级 3 中级 4 高级'),
                'certBn' => array('name' => 'cert_bn', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '证书编号'),
                'issued' => array('name' => 'issued', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '发证机关'),
                'occupation' => array('name' => 'occupation', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '职业'),
                'theoreticalScore' => array('name' => 'theoretical_score', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '理论知识成绩'),
                'operatingScore' => array('name' => 'operating_score', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '操作技能成绩'),
                'evaluationScore' => array('name' => 'evaluation_score', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '评定成绩'),
                'trainOrganization' => array('name' => 'train_organization', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '培训机构'),
                'accreditationTime' => array('name' => 'accreditation_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '认证时间'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
            ),
        );
    }


    /**
     * 添加职业技能证书
     * #desc 用于添加职业技能证书
     * #return int code 操作码，0表示成功
     * #return int cert_id  证书ID
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
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'type' => $this->type,
            'img_url' => json_encode($this->imgUrl),
            'name' => $this->name,
            'level' => $this->level,
            'cert_bn' => $this->certBn,
            'issued' => $this->issued,
            'occupation' => $this->occupation,
            'theoretical_score' => $this->theoreticalScore,
            'operating_score' => $this->operatingScore,
            'evaluation_score' => $this->evaluationScore,
            'train_organization' => $this->trainOrganization,
            'accreditation_time' => strtotime($this->accreditationTime),
            'remark' => $this->remark,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $staffCertDomain = new Domain_Jiafubao_StaffSkillCert();
        //检测该等级证书是否存在
        $certInfo = $staffCertDomain->detectSkillCert($data);
        if(!empty($certInfo)){
            $rs['code'] = 184;
            $rs['msg'] = T('Cert level exists');
            return $rs;
        }
        $certId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $certId = $staffCertDomain->addSkillCert($data);
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

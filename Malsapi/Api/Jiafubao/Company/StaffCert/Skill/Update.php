<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Skill_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'certId' => array('name' => 'cert_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证书ID'),
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
     * 更新职业技能证书
     * #desc 用于更新职业技能证书
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
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
        //判断技能证书是否存在
        $staffCertDomain = new Domain_Jiafubao_StaffSkillCert();
        $certInfo = $staffCertDomain->getBaseInfo($this->certId);
        if( !$certInfo) {
            $rs['code'] = 157;
            $rs['msg'] = T('Skill cert not exists');
            return $rs;
        }
        //判断证书是否作废
        if($certInfo['status'] == 'n'){
            $rs['code'] = 166;
            $rs['msg'] = T('Cert have been repeal');
            return $rs;
        }
        $data = array(
            'cert_id' => $this->certId,
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
            'is_check' => 'n',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $certId = $staffCertDomain->updateSkillCert($data,$certInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if( $certId){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}

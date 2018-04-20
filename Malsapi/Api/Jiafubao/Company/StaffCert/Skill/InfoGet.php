<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Skill_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'certId' => array('name' => 'cert_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证书ID'),
            ),
        );
    }

    /**
     * 获取职业技能证书详情
     * #desc 用于获取职业技能证书详情
     * #return int code 操作码，0表示成功
     * #return int id 证书ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return int staff_name 家政员名称
     * #return string type 证书类型:society 协会  government 政府
     * #return int level 证书等级 1 专项级 2 初级 3 中级 4 高级
     * #return string name 技能名称
     * #return string cert_bn 证书编号
     * #return array img_url 图片路径
     * #return string issued 发证机关
     * #return string occupation 职业
     * #return string theoretical_score 理论知识考核成绩
     * #return string operating_score 操作技能考核成绩
     * #return string evaluation_score 评定成绩
     * #return string train_organization 培训机构
     * #return int accreditation_time 认证时间
     * #return string status 状态: y 正常 n 作废
     * #return string is_default 是否默认使用 y 是 n 否
     * #return string remark 备注
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
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
        $rs['info'] = $certInfo;

        return $rs;
    }
    
}

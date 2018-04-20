<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_Cert_Ability_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'certId' => array('name' => 'cert_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证书ID'),
            ),
        );
    }

    /**
     * 获取专项能力证书详情
     * #desc 用于获取专项能力证书详情
     * #return int code 操作码，0表示成功
     * #return int id 证书ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return array img_url 图片路径
     * #return int level 证书等级 1 专项级 2 初级 3 中级 4 高级
     * #return string cert_bn 证书编号
     * #return string issued 发证机关
     * #return string train_course 培训内容
     * #return int train_time 培训时间
     * #return string train_score 培训成绩
     * #return string train_periods 培训课时
     * #return string skill 所学技能
     * #return string train_organization 培训机构
     * #return string status 状态: y 正常 n 作废
     * #return string is_default 是否默认使用 y 是 n 否
     * #return string remark 备注
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return array staff_info 家政员信息
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

        //判断技能证书是否存在
        $staffCertDomain = new Domain_Jiafubao_StaffAbilityCert();
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

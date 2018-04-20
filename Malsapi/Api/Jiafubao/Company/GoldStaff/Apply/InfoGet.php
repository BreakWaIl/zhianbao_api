<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_GoldStaff_Apply_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
            ),
        );
    }
  
  /**
     * 获取金牌家政员申请详情
     * #desc 用于获取金牌家政员申请详情
     * #return int code 操作码，0表示成功
     * #return int id 申请ID
     * #return int company_id 公司ID
     * #return string name 家政员姓名
     * #return string trades 从事工种
     * #return string experience 从事家政服务时间
     * #return string birthday  出生年份
     * #return int mobile 手机号
     * #return string house_keep_card 家政卡号
     * #return string bank_card 银行卡号
     * #return string cardID 身份证号码
     * #return string skill_level 职业技能等级
     * #return string education 学历
     * #return string company_name 所属家政公司
     * #return string remark 备注
     * #return string years 申请年份
     * #return string status 申请状态:wait 等待 success 成功 refuse 拒绝
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return string log_list 申请日志
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

        //判断申请记录是否已存在
        $goldStaffDomain = new Domain_Jiafubao_CompanyGoldStaff();
        $applyInfo = $goldStaffDomain->applyInfo($this->applyId);
        if( !$applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }

        $rs['info'] = $applyInfo;

        return $rs;
    }
    
}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_GoldStaff_Apply_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true, 'desc'=> '申请ID'),
            ),
        );
    }
  
  /**
     * 获取申请金牌家政员详情
     * #desc 用于申请金牌家政员详情
     * #return int code 操作码，0表示成功
   * #return int company_id 公司ID
   * #return string name 员工姓名
   * #return string trades 从事工种
   * #return string experience 从事家政服务时间
   * #return string birthday 出生年份
   * #return int mobile 手机号
   * #return string house_keep_card 家政卡号
   * #return string bank_card 银行卡号
   * #return string cardID 身份证号
   * #return string skill_level 职业技能等级
   * #return string education 学历
   * #return string company_name 家政公司名称
   * #return string remark 备注
   * #return string years 申请年份
   * #return string status 状态:wait 等待 success 成功 refuse 拒绝
   * #return string log_list 申请日志
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

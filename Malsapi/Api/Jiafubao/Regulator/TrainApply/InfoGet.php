<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_TrainApply_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
            ),
        );
    }
  
  /**
     * 获取服务培训申请详情
     * #desc 用于获取服务培训申请详情
     * #return int code 操作码，0表示成功
     * #return int id 申请ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string staff_name 家政员姓名
     * #return string avatar 照片
     * #return string sex 性别
     * #return string birthday  出生日期
     * #return string cardID 身份证号码
     * #return string address 家庭地址
     * #return string age 年龄
     * #return string nation 民族
     * #return string marriage 婚姻情况
     * #return string education 学历
     * #return string industry 专业
     * #return int mobile 手机号
     * #return string company_telephone 联系电话
     * #return string work_objective 求职意向
     * #return array work_service 所选培训服务和费用
     * #return string total_cost 总费用
     * #return string status 申请状态 wait 等待, active 正常, accept 已接受, process 已处理 reject 已拒绝
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return int company_name 公司名称
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
        $trainApplyDomain = new Domain_Jiafubao_StaffTrainApply();
        $applyInfo = $trainApplyDomain->getBaseInfo($this->applyId);
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

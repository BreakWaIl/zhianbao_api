<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_HealthApply_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
            ),
        );
    }
  
  /**
     * 获取健康体检申请表详情
     * #desc 用于获取健康体检申请表详情
     * #return int code 操作码，0表示成功
     * #return int id 申请ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string staff_name 家政员姓名
     * #return string avatar 照片
     * #return string sex 性别
     * #return string education 文化程度
     * #return string native_place 籍贯
     * #return string birthday  出生日期
     * #return string now_district 现居住所属区
     * #return string now_address 联系地址
     * #return string cardID 身份证号码
     * #return string company_name 所属家政公司
     * #return int mobile 手机号
     * #return int telephone 联系电话
     * #return string service_style 服务形式: time 小时工 home 住家制 all 全日制
     * #return int service_year 从事服务年限
     * #return string reservation_time 预约时间
     * #return string reservation_address 预约地点
     * #return string contractors 承接单位
     * #return string status 申请状态 wait 等待, active 正常, accept 已接受, process 已处理 reject 已撤回
     * #return array apply_log 申请日志
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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
        $healthApplyDomain = new Domain_Jiafubao_StaffHealthApply();
        $applyInfo = $healthApplyDomain->getBaseInfo($this->applyId);
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

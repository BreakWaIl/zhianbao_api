<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_Insurance_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'recordId' => array('name' => 'record_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '记录ID'),
            ),
        );
    }

    /**
     * 获取保险记录详情
     * #desc 用于获取保险记录详情
     * #return int code 操作码，0表示成功
     * #return int id 保险记录ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string title 保险类别
     * #return string insured_name 投保人
     * #return string policy_bn 保单号
     * #return int end_time 截至有效期
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

        //判断保险记录是否存在
        $staffInsuranceDomain = new Domain_Jiafubao_StaffInsurance();
        $recordInfo = $staffInsuranceDomain->getBaseInfo($this->recordId);
        if( !$recordInfo) {
            $rs['code'] = 153;
            $rs['msg'] = T('Insurance record not exist');
            return $rs;
        }

        $rs['info'] = $recordInfo;

        return $rs;
    }
    
}

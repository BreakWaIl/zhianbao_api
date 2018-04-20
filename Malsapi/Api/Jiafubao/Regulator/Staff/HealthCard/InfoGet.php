<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_HealthCard_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'healthId' => array('name' => 'health_id', 'type' => 'int', 'min' => 0, 'require' => false, 'desc' => '健康卡ID'),
            ),
        );
    }
  
  /**
     * 获取健康卡详情
     * #desc 用于获取健康卡详情
     * #return int code 操作码，0表示成功
     * #return int id 健康卡ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string card_bn 健康卡编号
     * #return int send_time 发放时间
     * #return int end_time 截至有效期
     * #return array img_url 图片
     * #return status 状态
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return string staff_name  家政员名称
     * #return string sex 性别
     * #return string mobile  手机号码
     * #return string birthday 出生日期
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
        //检测健康卡是否已存在
        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $healthCardInfo = $healthCardDomain->getBaseInfo($this->healthId);
        if(!$healthCardInfo){
            $rs['code'] = 152;
            $rs['msg'] = T('Health card not exist');
            return $rs;
        }

        $rs['info'] = $healthCardInfo;

        return $rs;
    }
    
}

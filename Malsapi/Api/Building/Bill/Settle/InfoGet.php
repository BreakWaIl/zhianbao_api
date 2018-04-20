<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Settle_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'settleId' => array('name' => 'settle_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '账单ID'),
            ),
        );
    }

  /**
   * 获取结算单详情
   * #desc 用于获取结算单详情
   * #return int id 账单ID
   * #return int company_id 公司ID
   * #return string company_name 公司名称
   * #return int project_id 项目ID
   * #return string project_name 项目名称
   * #return int staff_id 员工ID
   * #return string staff_name 员工名称
   * #return string cardID 身份证号
   * #return string mobile 手机号
   * #return string start_time 开始时间
   * #return string end_time 结束时间
   * #return float work_day 工日
   * #return float work_price 工价
   * #return float total_amount 总计金额
   * #return float borrow_amount 借支金额
   * #return float balance_amount 余额
   * #return string remark 备注
   * #return string settle_status 结算状态: y 已结算 n 未结算
   * #return int operate_id 操作人ID
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断结算单是否存在
        $billSettleDomain = new Domain_Building_BillSettle();
        $settleInfo = $billSettleDomain->getBaseInfo($this->settleId);
        if (empty($settleInfo)) {
            $rs['code'] = 206;
            $rs['msg'] = T('Bill not exists');
            return $rs;
        }
        $rs['info'] = $settleInfo;

        return $rs;
    }
    
}

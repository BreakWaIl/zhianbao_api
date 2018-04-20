<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Sub_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'billId' => array('name' => 'bill_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '账单ID'),
            ),
        );
    }

  /**
   * 获取管理员出入账单详情
   * #desc 用于获取管理员出入账单详情
   * #return int id 账单ID
   * #return int company_id 公司ID
   * #return int sub_id 管理员ID
   * #return string sub_name 管理员ID
   * #return int project_id 项目ID
   * #return string project_name 项目名称
   * #return string type 类型：expenditure 支出, income 收入 borrow 借支
   * #return string title 出入账标题
   * #return float amount 出入金额
   * #return string remark 备注
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
        //判断账单是否存在
        $billSubDomain = new Domain_Building_BillSub();
        $billInfo = $billSubDomain->getBaseInfo($this->billId);
        if (empty($billInfo)) {
            $rs['code'] = 206;
            $rs['msg'] = T('Bill not exists');
            return $rs;
        }
        $rs['info'] = $billInfo;

        return $rs;
    }
    
}

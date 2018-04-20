<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Construction_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'logId' => array('name' => 'log_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '日志ID'),
            ),
        );
    }

  /**
   * 获取施工日志详情
   * #desc 用于获取施工日志详情
   * #return int code 操作码，0表示成功
   * #return int id 日志ID
   * #return int company_id 公司ID
   * #return string label_id 标签ID
   * #return string dateTime 记录日期
   * #return array day 白天信息
   * #return array night 晚上信息
   * #return string production_record 生产情况记录
   * #return string safety_work_record 技术质量安全工作记录
   * #return string contract_work_record 合同外工作量记录
   * #return string recorder 记录人
   * #return int operate_id 操作人ID
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   * #return array label_info 标签信息
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
        //判断日志是否存在
        $constructionDomain = new Domain_Building_Construction();
        $logInfo = $constructionDomain->getBaseInfo($this->logId);
        if (empty($logInfo)) {
            $rs['code'] = 204;
            $rs['msg'] = T('Construction log not exists');
            return $rs;
        }
        $rs['info'] = $logInfo;

        return $rs;
    }
    
}

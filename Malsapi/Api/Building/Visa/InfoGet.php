<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Visa_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'visaId' => array('name' => 'visa_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '签证ID'),
            ),
        );
    }

  /**
   * 获取合同外签证详情
   * #desc 用于获取合同外签证详情
   * #return int code 操作码，0表示成功
   * #return int id 签证ID
   * #return int company_id 公司ID
   * #return int project_id 项目ID
   * #return string title 标题
   * #return array img_url 图片地址
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   * #return int operate_id 操作人ID
   * #return string project_name 项目名称
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
        //判断合同外签证是否存在
        $visaDomain = new Domain_Building_Visa();
        $visaInfo = $visaDomain->getBaseInfo($this->visaId);
        if (empty($visaInfo)) {
            $rs['code'] = 205;
            $rs['msg'] = T('Visa outside the contract not exists');
            return $rs;
        }
        $rs['info'] = $visaInfo;

        return $rs;
    }
    
}

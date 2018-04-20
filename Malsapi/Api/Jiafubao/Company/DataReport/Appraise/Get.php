<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_DataReport_Appraise_Get extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
		);
 	}
  
  /**
     * 获取公司服务平均分统计
     * #desc 用于获取公司服务平均分统计
     * #return int code 操作码，0表示成功
     * #return int orderTotal 订单总数
     * #return int staffFraction 家政员评分
     * #return int customerFraction 客户评分
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

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $dataReportDomain = new Domain_Jiafubao_DataReport();
        $info = $dataReportDomain->averageFraction($filter);
        $rs['info'] = $info;

        return $rs;
    }
	
}

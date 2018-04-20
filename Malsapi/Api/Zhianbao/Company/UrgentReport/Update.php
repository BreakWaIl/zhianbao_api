<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_UrgentReport_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'reportId' => array('name' => 'report_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '演练ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '演练名称'),
                     'content' => array('name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '演练内容'),
                     'number' => array('name' => 'number', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '人员数量'),
                     'result' => array('name' => 'result', 'type' => 'string', 'require' => true, 'desc' => '演练结果'),
            ),
		);
 	}
	
  
  /**
     * 更新应急演练
     * #desc 用于更新应急演练
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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

        //判断应急演练是否存在
        $reportDomain = new Domain_Zhianbao_UrgentReport();
        $reportInfo = $reportDomain->getBaseInfo($this->reportId);
        if( !$reportInfo) {
            DI()->logger->debug('Urgent report not exist', $this->reportId);

            $rs['code'] = 141;
            $rs['msg'] = T('Urgent report not exist');
            return $rs;
        }
        $endTime = strtotime($reportInfo['create_time']) + 86400 * 7;
        if( time() > $endTime){
            DI()->logger->debug('Exceeds the update time', $this->reportId);

            $rs['code'] = 142;
            $rs['msg'] = T('Exceeds the update time');
            return $rs;
        }
        $data = array(
            'report_id' => $this->reportId,
            'name' => $this->name,
            'content' => $this->content,
            'number' => $this->number,
            'result' => $this->result,
            'last_modify' => time(),
        );
        $res = $reportDomain->updateReport($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

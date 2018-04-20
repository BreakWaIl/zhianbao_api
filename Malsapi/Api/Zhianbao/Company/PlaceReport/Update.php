<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_PlaceReport_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'reportId' => array('name' => 'report_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '检测ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '人员名称'),
                     'imgUrl' => array('name' => 'img_url', 'type' => 'string', 'require' => true, 'desc' => '图片路径'),
            ),
		);
 	}
	
  
  /**
     * 更新检测报告
     * #desc 用于更新检测报告
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

        //判断检测报告是否存在
        $reportDomain = new Domain_Zhianbao_PlaceReport();
        $reportInfo = $reportDomain->getBaseInfo($this->reportId);
        if( !$reportInfo) {
            DI()->logger->debug('Place report not found', $this->reportId);

            $rs['code'] = 125;
            $rs['msg'] = T('Place report not exists');
            return $rs;
        }

        $data = array(
            'report_id' => $this->reportId,
            'name' => $this->name,
            'img_url' => $this->imgUrl,
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

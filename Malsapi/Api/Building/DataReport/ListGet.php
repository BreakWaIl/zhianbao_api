<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_DataReport_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'beginTime' => array('name' => 'begin_time', 'type'=>'string', 'require'=> false,'desc'=> '开始时间'),
                     'endTime' => array('name' => 'end_time', 'type'=>'string', 'require'=> false,'desc'=> '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
            ),
		);
 	}

  /**
     * 获取项目数据统计
     * #desc 用于获取项目数据统计
     * #return int code 操作码，0表示成功
     * #return int start_time 开始时间
     * #return int stop_time 结束时间
     * #return string budgetTotal 预算人天
     * #return string signTotal 实际人天
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
        //判断公司项目是否存在
        $projectDomain = new Domain_Building_Project();
        $projectInfo = $projectDomain->getBaseInfo($this->projectId);
        if (empty($projectInfo)) {
            $rs['code'] = 192;
            $rs['msg'] = T('Project not exists');
            return $rs;
        }

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['project_id'] = $this->projectId;
        //最近7天的情况
        if(empty($this->beginTime) && empty($this->endTime)){
            $filter['beginTime'] = strtotime(date("Y-m-d",time()-7*86400));
            $filter['endTime'] = strtotime(date("Y-m-d",time()));
        }else{
            $filter['beginTime'] = strtotime($this->beginTime);
            $filter['endTime'] = strtotime($this->endTime) + 86400;
        }
        $todayTime = strtotime(date("Ymd",time()));
        if($filter['endTime'] > $todayTime){
            $rs['code'] = 197;
            $rs['msg'] = T('Can not exceed now date');
            return $rs;
        }
        $dateReportDomain = new Domain_Building_DataReport();
        $list = $dateReportDomain->getProjectCost($projectInfo,$filter,$this->page,$this->pageSize);
        if( !$list){
            $rs['code'] = 189;
            $rs['msg'] = T('The query time can not exceed 90 days');
            return $rs;
        }else{
            $day = $list['day'];
            unset($list['day']);
            $total = $day;
        }

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}

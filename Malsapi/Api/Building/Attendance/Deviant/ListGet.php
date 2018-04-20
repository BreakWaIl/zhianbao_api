<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Attendance_Deviant_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '班组ID'),
                     'time' => array('name' => 'time', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '当前日期'),
                     'type' => array('name' => 'type', 'type'=>'enum','range' => array('absent','active','never'), 'default' => 'active', 'require'=> true,'desc'=> 'absent 异常, active 正常,never 未签到 '),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

  /**
     * 获取项目考勤记录
     * #desc 用于获取项目考勤记录
     * #return int code 操作码，0表示成功
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
        //判断班组是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfo($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }

        $AttendanceDomain = new Domain_Building_Attendance();
        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['project_id'] = $this->projectId;
        //获取当前项目班组下的人员
        $staffIds = $AttendanceDomain->catToStaff($filter,$this->catId);
        $filter['time'] = $this->time;
        $filter['status'] = $this->type;

        $list = $AttendanceDomain->getAllSignStaff($companyInfo,$projectInfo,$filter,$this->page,$this->pageSize,$this->orderby,$staffIds);
        $total = $AttendanceDomain->getSignStaffCount($filter,$staffIds);
//        $total = $list['total'];unset($list['total']);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}

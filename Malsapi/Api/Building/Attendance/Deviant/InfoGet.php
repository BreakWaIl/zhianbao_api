<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Attendance_Deviant_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'time' => array('name' => 'time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '所选日期'),
            ),
        );
    }
  
  /**
     * 获取考勤异常处理结果
     * #desc 用于获取考勤异常处理结果
     * #return int code 操作码，0表示成功
     * #return int staff_id 员工ID
     * #return int company_id 公司ID
     * #return int project_id 项目ID
     * #return int record_time 记录时间
     * #return string cause 异常原因
     * #return int cost 人天成本
     * #return string remark 备注
     * #return string company_name 公司名称
     * #return string staff_name 人员名称
     * #return string project_name 项目名称
     * #return int create_time 创建时间
     * #return int last_modify 最后更新时间
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
        //判断员工是否存在
        $staffDomain = new Domain_Building_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['project_id'] = $this->projectId;
        $filter['staff_id'] = $this->staffId;
        $filter['record_time'] = strtotime($this->time);

        $AttendanceDomain = new Domain_Building_Attendance();
        $info = $AttendanceDomain->deviantInfo($filter);

        $rs['info'] = $info;

        return $rs;
    }
    
}

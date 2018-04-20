<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Attendance_Deviant_Process extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'time' => array('name' => 'time', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '当前日期'),
                     'cost' => array('name' => 'cost', 'type' => 'float',  'require' => true, 'desc' => '人天成本'),
                     'remark' => array('name' => 'remark', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '备注'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  /**
     * 获取处理异常考勤
     * #desc 用于获取处理异常考勤
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
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

        $data = array();
        $data['company_id'] = $this->companyId;
        $data['project_id'] = $this->projectId;
        $data['staff_id'] = $this->staffId;
        $data['cost'] = $this->cost;
        $data['remark'] = $this->remark;
        $data['record_time'] = $this->time;
    //    print_r($data);exit;
        $AttendanceDomain = new Domain_Building_Attendance();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $info = $AttendanceDomain->processDeviant($data,$this->operateId);
            if( $info){
                $status = 0;
            }else{
                $status = 1;
            }
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}

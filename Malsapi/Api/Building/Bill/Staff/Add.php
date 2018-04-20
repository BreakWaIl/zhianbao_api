<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Staff_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'type' => array('name' => 'type', 'type'=>'enum','range' => array('expenditure','income','borrow'), 'default' => 'expenditure', 'require'=> true,'desc'=> '类型：expenditure 支出, income 收入 borrow 借支'),
                'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '出入账标题'),
                'amount' => array('name' => 'amount', 'type' => 'float', 'require' => true, 'desc' => '出入金额'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加员工出入账单
     * #desc 用于添加员工出入账单
     * #return int code 操作码，0表示成功
     * #return int bill_id 账单ID
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
        //判断员工是否存在
        $staffDomain = new Domain_Building_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
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
        //检测项目和员工关系
        $billStaffDomain = new Domain_Building_BillStaff();
        $filter = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'project_id' => $this->projectId,
        );
        $check = $billStaffDomain->check($filter);
        if( !$check){
            $rs['code'] = 207;
            $rs['msg'] = T('No staff in this project');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'project_id' => $this->projectId,
            'type' => $this->type,
            'title' => $this->title,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $billId = $billStaffDomain->add($data,$projectInfo);
            $rs['info']['bill_id'] = $billId;
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }



        return $rs;
    }

}

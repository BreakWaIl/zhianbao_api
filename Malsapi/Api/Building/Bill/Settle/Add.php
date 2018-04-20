<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Settle_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                'workPrice' => array('name' => 'work_price', 'type' => 'float', 'require' => true, 'desc' => '工价'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加结算单
     * #desc 用于添加结算单
     * #return int code 操作码，0表示成功
     * #return int settle_id 结算单ID
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
        $data = array(
            'company_id' => $this->companyId,
            'company_name' => $companyInfo['name'],
            'project_id' => $this->projectId,
            'project_name' => $projectInfo['name'],
            'staff_id' => $this->staffId,
            'staff_name' => $staffInfo['name'],
            'cardID' => $staffInfo['cardID'],
            'mobile' => $staffInfo['mobile'],
            'work_price' => $this->workPrice,
            'remark' => $this->remark,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );

        $billSettleDomain = new Domain_Building_BillSettle();
        $settleId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $settleId = $billSettleDomain->add($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['settle_id'] = $settleId;

        return $rs;
    }

}

<?php
class Domain_Zhianbao_DashBoard {

	//获取通知详情
    public function getBaseInfo($companyId){
        $rs = array ();

        //获取待签收通知数量
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $notice_filter = array(
            'company_id' => $companyId,
            'is_sign' => 'n'
        );
        $noticeTotal = $noticeToReleaseModel->getCount($notice_filter);
        //待上传人员
        $staffModel = new Model_Zhianbao_Staff();
        $staffCertModel = new Model_Zhianbao_Cert();
        //获取人员总数
        $staff_filter = array('company_id' => $companyId);
        $staff_list = $staffModel->getAll ('*', $staff_filter );
        $staffTotal = 0;
        foreach ($staff_list as $key=>$value){
            $cert_fiter = array('company_id' => $companyId,'staff_id' => $value['id']);
            $staff_cert = $staffCertModel->getByWhere($cert_fiter, '*');
            if(empty($staff_cert)){
                $staffTotal++;
            }
        }
        //隐患排除与整改
        $checkPlanDomain = new Domain_Zhianbao_CheckPlan();
        $checkPlanInfo = $checkPlanDomain->getLastCheck($companyId); //隐患、已更改、未更改、最近一次
        //待处罚记录
        $checkTroubleModel = new Model_Zhianbao_CheckTrouble();
        $trouble_filter = array('company_id' => $companyId, 'status' => '0');
        $troubleTotal = $checkTroubleModel->getCount($trouble_filter);
        //待审核自评数
        $safeApplyModel = new Model_Zhianbao_SafeApply();
        $safe_filter = array('company_id' => $companyId);
        $safe_filter['status'] = 'wait';
        $waitTotal = $safeApplyModel->getCount($safe_filter);
        $safe_filter['status'] = 'applying';
        $applyTotal = $safeApplyModel->getCount($safe_filter);
        $safeSelfTotal = $waitTotal+$applyTotal;

        $rs['noticeTotal'] = $noticeTotal;
        $rs['staffTotal'] = $staffTotal;
        $rs['staffTotal'] = $staffTotal;
        $rs['checkPlanInfo'] = $checkPlanInfo;
        $rs['troubleTotal'] = $troubleTotal;
        $rs['safeSelfTotal'] = $safeSelfTotal;
        return $rs;
    }


}

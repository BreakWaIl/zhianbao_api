<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_TimingPlan_Manual extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'time' => array('name' => 'time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '所选日期'),
            ),
        );
    }

    /**
     * 更新当前日期项目人工信息
     * #desc 用于更新当前日期项目人工信息
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //判断是否已完成
        if($projectInfo['status'] == 'finish') {
            $rs['code'] = 211;
            $rs['msg'] = T('Project finish');
            return $rs;
        }
        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['project_id'] = $this->projectId;
        $filter['record_time'] = $this->time;
        $time = strtotime(date("Ymd",time()));
        if($filter['record_time'] > $time){
            $rs['code'] = 197;
            $rs['msg'] = T('Can not exceed now date');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $statisticsDomain = new Domain_Building_Statistics();
            $res = $statisticsDomain->manualDay($filter);
            if( $res){
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

        $rs['info']['status'] = $status;

        return $rs;
    }

}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Construction_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'labelId' => array('name' => 'label_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '标签ID,多个标签用‘，’分隔'),
                'dateTime' => array('name' => 'dateTime', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '记录日期'),
                'day' => array('name' => 'day', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '白天信息'),
                'night' => array('name' => 'night', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '夜间信息'),
                'production' => array('name' => 'production_record', 'type' => 'string', 'min' => 20, 'require' => true, 'desc' => '生产情况记录'),
                'safetyWork' => array('name' => 'safety_work', 'type' => 'string', 'require' => false, 'desc' => '技术质量安全工作记录'),
                'contractWork' => array('name' => 'contract_work', 'type' => 'string', 'require' => false, 'desc' => '合同外工作量记录'),
                'recorderId' => array('name' => 'recorder', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '记录人'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加施工日志
     * #desc 用于添加施工日志
     * #return int code 操作码，0表示成功
     * #return int log_id 日志ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测主账号是否存在
        $userDomain = new Domain_Zhianbao_User();
        $info = $userDomain->getBaseInfo($this->userId);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('User not found');
            return $rs;
        }
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断项目是否存在
        $projectDomain = new Domain_Building_Project();
        $projectInfo = $projectDomain->getBaseInfo($this->projectId);
        if (empty($projectInfo)) {
            $rs['code'] = 192;
            $rs['msg'] = T('Project not exists');
            return $rs;
        }
        //判断标签是否存在
        $constructionDomain = new Domain_Building_Construction();
        $filter = array(
            'company_id' => $this->companyId,
            'label_id' => $this->labelId,
        );
        $labelInfo = $constructionDomain->labelInfo($filter);
        if ( !$labelInfo) {
            $rs['code'] = 203;
            $rs['msg'] = T('Label not exists');
            return $rs;
        }
        //判断记录人是否存在
        $subDomain = new Domain_Building_SubAccount();
        $subInfo = $subDomain->getBaseInfo($this->recorderId);
        if ( !$subInfo) {
            $rs['code'] = 215;
            $rs['msg'] = T('sub account not exists');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
            'label_id' => $this->labelId,
            'dateTime' => strtotime($this->dateTime),
            'day' => json_encode($this->day),
            'night' => json_encode($this->night),
            'production_record' => $this->production,
            'safety_work_record' => $this->safetyWork,
            'contract_work_record' => $this->contractWork,
            'recorder' => $this->recorderId,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $logId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $logId = $constructionDomain->add($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['log_id'] = $logId;

        return $rs;
    }

}

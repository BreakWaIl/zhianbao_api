<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Visa_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '标题'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片地址'),
                'remark' => array('name' => 'remark', 'type'=>'string', 'require'=> false,'desc'=> '备注'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加合同外签证
     * #desc 用于添加合同外签证
     * #return int code 操作码，0表示成功
     * #return int visa_id 签证ID
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
        $data = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
            'title' => $this->title,
            'img_url' => json_encode($this->imgUrl),
            'remark' => $this->remark,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $visaDomain = new Domain_Building_Visa();
        $visaId = $visaDomain->add($data);
        if ( !$visaId) {
            $rs['code'] = 102;
            $rs['msg'] = T('Add failed');
            return $rs;
        }

        $rs['info']['visa_id'] = $visaId;

        return $rs;
    }

}

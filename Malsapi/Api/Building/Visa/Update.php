<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Visa_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'visaId' => array('name' => 'visa_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '签证ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '标题'),
                     'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片地址'),
                     'remark' => array('name' => 'remark', 'type'=>'string', 'require'=> false,'desc'=> '备注'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  
  /**
     * 更新合同外签证
     * #desc 用于更新合同外签证
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断合同外签证是否存在
        $visaDomain = new Domain_Building_Visa();
        $visaInfo = $visaDomain->getBaseInfo($this->visaId);
        if (empty($visaInfo)) {
            $rs['code'] = 205;
            $rs['msg'] = T('Visa outside the contract not exists');
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
            'visa_id' => $this->visaId,
            'project_id' => $this->projectId,
            'title' => $this->title,
            'img_url' => json_encode($this->imgUrl),
            'remark' => $this->remark,
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $visaDomain = new Domain_Building_Visa();
        $res = $visaDomain->update($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}


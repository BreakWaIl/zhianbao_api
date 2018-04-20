<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_SafeSelf_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申报ID'),
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申报人ID'),
                     'title' => array('name' => 'title', 'type' => 'string', 'min' => 0, 'require' => false, 'desc' => '申请标题'),
                     'templateId' => array('name' => 'template_id', 'type' => 'int', 'default'=> '0', 'require' => false, 'desc' => '模板ID'),
                     'grade' => array('name' => 'grade', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请等级'),
                     'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '自评内容'),
                     'file' => array('name' => 'file', 'type' => 'string', 'require' => false, 'desc' => '文件'),
            ),
		);
 	}
	
  
  /**
     * 更新安全生产标准化申报
     * #desc 用于更新安全生产标准化申报
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断申请是否存在
        $applyDomain = new Domain_Zhianbao_SafeApply();
        $applyInfo = $applyDomain->getBaseInfo($this->applyId);
        if(! $applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        if($applyInfo['status'] == 'applying' || $applyInfo['status'] == 'review' || $applyInfo['status'] == 'firstReview' || $applyInfo['status'] == 'finish'){
            DI()->logger->debug('Ban apply update', $this->applyId);

            $rs['code'] = 120;
            $rs['msg'] = T('Ban apply update');
            return $rs;
        }

        //判断用户是否存在
        $domainUser = new Domain_Zhianbao_User();
        $userInfo = $domainUser->getBaseInfo($this->userId);
        if (empty($userInfo)) {
            DI()->logger->debug('Company not exists', $this->userId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $data = array(
            'apply_id' => $this->applyId,
            'user_id' => $this->userId,
            'user_name' => $userInfo['name'],
            'apply_title' => $this->title,
            'template_id' => $this->templateId,
            'apply_grade' => $this->grade,
            'self_content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
            'file_path' => $this->file,
            'status' => 'wait',
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyDomain = new Domain_Zhianbao_SafeApply();
            $res = $applyDomain->updateApply($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

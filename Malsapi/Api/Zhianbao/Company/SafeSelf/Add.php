<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeSelf_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				'Go' => array (
                    'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                    'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申报人ID'),
                    'title' => array('name' => 'title', 'type' => 'string', 'require' => false, 'desc' => '申请标题'),
                    'templateId' => array('name' => 'template_id', 'type' => 'int', 'default'=> '0', 'require' => false, 'desc' => '模板ID'),
                    'grade' => array('name' => 'grade', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请等级'),
                    'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '自评内容'),
                    'file' => array('name' => 'file', 'type' => 'string', 'require' => false, 'desc' => '文件'),
				)
		);
 	}


  	/**
     * 添加安全生产标准化申报
     * #desc 用于安全生产标准化申报
     * #return int code 操作码，0表示成功
     * #return int apply_id 申请ID
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
            'user_id' => $this->userId,
			'company_id' => $this->companyId,
            'user_name' => $userInfo['name'],
			'company_name' => $companyInfo['name'],
			'apply_title' => $this->title,
            'template_id' => $this->templateId,
			'apply_grade' => $this->grade,
			'self_content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
            'file_path' => $this->file,
		);
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyDomain = new Domain_Zhianbao_SafeApply();
            $applyId = $applyDomain->addApply($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

		$rs['apply_id'] = $applyId;
        return $rs;
    }
	
}

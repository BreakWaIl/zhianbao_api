<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
                     'name' => array('name' => 'name', 'type' => 'string' , 'require' => true, 'desc' => '个人姓名'),
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'int', 'require' => false, 'desc' => '监管者ID'),
                     'logoImg' => array('name' => 'logo_img',  'type' => 'string',  'require' => false, 'desc' => '头像'),
            ),
		);
 	}

  
  /**
     * 商户资料更新
     * #desc 用于商户的更新
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测是否存在
        $domain = new Domain_Zhianbao_User();
        $info = $domain->getBaseInfo($this->userId);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $data = array('name' => $this->name,'logoImg' => $this->logoImg);
        if(isset($this->logoImg)){
            $data['logoImg'] = $this->logoImg;
        }
        if(isset($this->regulatorId)){
            $data['regulator_id'] = $this->regulatorId;
        }
        //验证激活码
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $userId = $domain->updateCompany($this->userId,$data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['user_id'] = $userId;

        return $rs;
    }

}


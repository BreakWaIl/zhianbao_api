<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
            ),
		);
 	}

  
  /**
     * 商户资料获取
     * #desc 用于商户的获取
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
        $companyDomain = new Domain_Zhianbao_Company();
        if($info['parent_id'] == 0) {
            $companyInfo = $companyDomain->getBaseByUserId($this->userId);
        }else{
            $companyInfo = $companyDomain->getBaseByUserId($info['parent_id']);
        }
        $info['company_info'] = $companyInfo;
        $rs['info'] = $info;
        return $rs;
    }

}


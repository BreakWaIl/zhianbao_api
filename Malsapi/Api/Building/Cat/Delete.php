<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Cat_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司类别ID'),
            ),
		);
 	}
	
  
  /**
     * 删除公司类别
     * #desc 用于删除当前公司类别
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        ///判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断公司类别是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfo($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }

        //判断是否在使用
        $isUser = $catDomain->isUser($this->companyId,$this->catId);
        if( !$isUser){
            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }
        $res = $catDomain->delete($this->catId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}

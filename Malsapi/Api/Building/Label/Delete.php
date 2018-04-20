<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Label_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'labelId' => array('name' => 'label_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '标签ID'),
            ),
		);
 	}
	
  
  /**
     * 删除标签
     * #desc 用于删除标签
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
        //判断标签是否存在
        $labelDomain = new Domain_Building_Label();
        $labelInfo = $labelDomain->getBaseInfo($this->labelId);
        if (empty($labelInfo)) {
            $rs['code'] = 203;
            $rs['msg'] = T('Label not exists');
            return $rs;
        }
        //判断是否在使用
        $isUser = $labelDomain->isUser($this->companyId,$this->labelId);
        if( !$isUser){
            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }
        $res = $labelDomain->delete($this->labelId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}

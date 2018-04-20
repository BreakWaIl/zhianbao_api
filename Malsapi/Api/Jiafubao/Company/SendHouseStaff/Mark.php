<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_SendHouseStaff_Mark extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家服云公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'mark' => array('name' => 'mark', 'type' => 'string', 'require' => true, 'desc' => '备注内容'),
            ),
		);
 	}
	
  
  /**
     * 添加推荐家政员备注
     * #desc 用于添加推荐家政员备注
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_CompanyShareHouseStaff();
        $filter = array(
            'company_id' => $this->jfbCompanyId,
            'staff_id' => $this->staffId
        );
        $res = $domain->addMark($filter,$this->mark);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

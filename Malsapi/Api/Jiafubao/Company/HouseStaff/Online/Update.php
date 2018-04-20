<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Jiafubao_Company_HouseStaff_Online_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'online' => array('name' => 'online', 'type'=>'enum','range' => array('y','n'), 'default' => 'y', 'require'=> true,'desc'=> '是否线上预约：y 是 n 否'),
            ),
		);
 	}
	
  
  /**
     * 更新家政员线上管理
     * #desc 用于更新家政员线上管理
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $data = array(
            'staff_id' => $this->staffId,
            'online' => $this->online,
            'last_modify' => time(),
        );
        $res = $houseStaffDomain->updateHouseStaff($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Jiafubao_Company_HouseStaff_IsPost extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'isPost' => array('name' => 'is_post', 'type'=>'enum','range' => array('y','n'), 'require'=> true,'desc'=> '是否上岗：y 是 n 否'),
            ),
		);
 	}
	
  
  /**
     * 更新家政员上下岗
     * #desc 用于更新家政员上下岗
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
        //判断是否已上岗
        if($this->isPost == 'y'){
            if($staffInfo['is_post'] == 'y'){
                $rs['code'] = 223;
                $rs['msg'] = T('Staff has been post');
                return $rs;
            }
        }
        //判断是否已下岗
        if($this->isPost == 'n'){
            if($staffInfo['is_post'] == 'n'){
                $rs['code'] = 224;
                $rs['msg'] = T('Staff has been laid off');
                return $rs;
            }
            //判断家政员是否使用中
            if($this->isPost == 'n'){
                $filter = array('staff_id' => $this->staffId);
                $isUser = $houseStaffDomain->isUserOrder($filter);
                if( !$isUser){
                    $rs['code'] = 137;
                    $rs['msg'] = T('Being used');
                    return $rs;
                }
            }
        }

        $data = array(
            'staff_id' => $this->staffId,
            'is_post' => $this->isPost,
            'last_modify' => time(),
        );

        $res = $houseStaffDomain->isPostHouseStaff($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Staff_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id','type'=>'int','require'=> true,'desc'=> '人员ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> true,'desc'=> '员工姓名'),
                     'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'),  'require'=> true,'desc'=> '员工性别'),
                     'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 11,  'require'=> true,'desc'=> '联系方式'),
                     'birthday' => array('name' => 'birthday', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '出生日期'),
                     'isCareer' => array('name' => 'is_career', 'type'=>'enum','range' => array('y','n'), 'default' => 'y', 'require'=> true,'desc'=> '是否专职:y 专职 n 兼职'),
                     'partId' => array('name' => 'part_id','type'=>'int','require'=> true,'desc'=> '员工角色'),
            ),
		);
 	}
  
  /**
   * 更新员工信息
   * #desc 用于更新员工信息
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'name' => $this->name,
            'sex' => $this->sex,
            'mobile' => $this->mobile,
            'last_modify' => time(),
            'birthday' => strtotime($this->birthday),
            'is_career' => $this->isCareer,
            'part_id' => $this->partId,
        );
        DI ()->notorm->beginTransaction ( 'db_api' );
        try {
            if($staffInfo['part_id'] != $data['part_id']){
                //删除员工之前上传的持证信息
                $certDomain = new Domain_Zhianbao_Cert();
                $certDomain->deleteStaffCert($staffInfo);
            }
            $status = $staffDomain->updateStaff($this->staffId,$data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        if($status){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}

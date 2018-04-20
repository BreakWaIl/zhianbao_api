<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_GoldStaff_Apply_Refuse extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '监管者ID'),
                     'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true, 'desc'=> '申请ID'),
            ),
		);
 	}
  
  /**
     * 拒绝申请
     * #desc 用于获取拒绝金牌家政员申请
     * #return int code 操作码，0表示成功
     * #return string status 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        //判断申请记录是否已存在
        $goldStaffDomain = new Domain_Jiafubao_CompanyGoldStaff();
        $applyInfo = $goldStaffDomain->applyInfo($this->applyId);
        if( !$applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        //判断申请是否拒绝
        if($applyInfo['status'] == 'refuse'){
            $rs['code'] = 175;
            $rs['msg'] = T('Apply have been reject');
            return $rs;
        }
        //判断申请是否成功
        if($applyInfo['status'] == 'success'){
            $rs['code'] = 128;
            $rs['msg'] = T('Apply is already finish');
            return $rs;
        }
        $filter = array('regulator_id' => $this->regulatorId, 'company_id'=>$applyInfo['company_id']);
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $goldStaffDomain = new Domain_Jiafubao_GoldStaff();
            $info = $goldStaffDomain->refuseApply($filter,$applyInfo);
            if( !$info){
                $status = 1;
            }else{
                $status = 0;
            }
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPlan_Check extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'planId' => array('name' => 'plan_id','type'=>'int','require'=> true,'desc'=> '检查计划ID'),
                     'checkResult' => array('name' => 'check_result','type'=>'array', 'format' => 'json', 'require'=> true,'desc'=> '检查结果 array(array("project_id"=> 1,"status" => 0,"message" => "原因"))  0:待检查 1:安全 2:不安全 '),
            ),
		);
 	}
  
  /**
   * 更新检查计划结果
   * #desc 用于更新检查计划结果
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看检查计划是否存在
        $planDomain = new Domain_Zhianbao_CheckPlan();
        $planInfo = $planDomain->getBaseInfo($this->planId);
        if(! $planInfo){
            $rs['code'] = 110;
            $rs['msg'] = T('Check plan not exists');
            return $rs;
        }
        if($planInfo['status'] == 2){
            $rs['code'] = 135;
            $rs['msg'] = T('Check plan already finish');
            return $rs;
        }
        $planDomain = new Domain_Zhianbao_CheckPlan();

        try {

            DI ()->notorm->beginTransaction ( 'db_api' );
            $addRs = $planDomain->doCheck($this->planId,$this->checkResult);
            DI ()->notorm->commit( 'db_api' );
        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['check_plan_id'] = $addRs;
        return $rs;
    }
	
}

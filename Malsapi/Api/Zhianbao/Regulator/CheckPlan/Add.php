<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPlan_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '检查计划标题'),
                     'content' => array('name' => 'content', 'type'=>'array', 'format'=> 'json', 'require'=> true,'desc'=> '检查计划内容-隐患项目ID'),
                     'checkTime' => array('name' => 'check_time','type'=>'int','require'=> true,'desc'=> '计划检查时间'),
                     'mark' => array('name' => 'mark','type'=>'string','require'=> false,'desc'=> '备注'),
            ),
		);
 	}
  
  /**
   * 添加检查计划
   * #desc 用于添加检查计划
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $planDomain = new Domain_Zhianbao_CheckPlan();
        $data = array(
            'company_id' => $this->companyId,
            'title' => $this->title,
            'content' => $this->content,
            'check_time' => $this->checkTime,
            'status' => 0,
            'create_time' => time(),
            'last_modify' => time(),
            'mark' => $this->mark
        );

        try {

            DI ()->notorm->beginTransaction ( 'db_api' );
            $addRs = $planDomain->addCheckPlan($data);
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

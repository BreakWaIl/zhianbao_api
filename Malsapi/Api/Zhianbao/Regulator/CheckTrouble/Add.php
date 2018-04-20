<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckTrouble_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '事故标题'),
                     'content' => array('name' => 'content', 'type'=>'string',  'require'=> true,'desc'=> '事故内容'),
            ),
		);
 	}
  
  /**
   * 添加事故记录
   * #desc 用于添加事故记录
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

        $planDomain = new Domain_Zhianbao_CheckTrouble();
        $data = array(
            'company_id' => $this->companyId,
            'title' => $this->title,
            'content' => $this->content,
            'status' => 0,
            'create_time' => time(),
            'last_modify' => time(),
        );

        try {

            DI ()->notorm->beginTransaction ( 'db_api' );
            $addRs = $planDomain->addCheckTrouble($data);
            DI ()->notorm->commit( 'db_api' );
        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['check_trouble_id'] = $addRs;
        return $rs;
    }
	
}

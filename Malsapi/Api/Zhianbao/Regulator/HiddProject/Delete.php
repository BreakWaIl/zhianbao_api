<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddProject_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'projectId' => array('name' => 'project_id','type'=>'int','require'=> true,'desc'=> '隐患项目ID'),
            ),
		);
 	}
  
  /**
   * 删除隐患项目
   * #desc 用于删除隐患项目
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $hiddProjectDomain = new Domain_Zhianbao_HiddProject();
        $delRs = $hiddProjectDomain->delHiddProject($this->projectId);
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}

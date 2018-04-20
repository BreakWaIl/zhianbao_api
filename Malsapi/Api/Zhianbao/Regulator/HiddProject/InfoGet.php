<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddProject_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'projectId' => array('name' => 'project_id','type'=>'int','require'=> true,'desc'=> '隐患项目ID'),
            ),
		);
 	}

  
  /**
     * 获取隐患项目详情
     * #desc 用于获取隐患项目详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患项目是否存在
        $hiddProjectDomain = new Domain_Zhianbao_HiddProject();
        $projectInfo = $hiddProjectDomain->getBaseInfo($this->projectId);
        if(! $projectInfo){
            $rs['code'] = 103;
            $rs['msg'] = T('Hidd type not exists');
            return $rs;
        }

        $rs['info'] = $projectInfo;
        return $rs;
    }

}


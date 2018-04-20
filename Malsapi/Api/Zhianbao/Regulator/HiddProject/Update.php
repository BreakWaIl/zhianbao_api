<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddProject_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'projectId' => array('name' => 'project_id','type'=>'int','require'=> true,'desc'=> '隐患项目ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> true,'desc'=> '隐患类型ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '隐患项目标题'),
                     'content' => array('name' => 'content','type'=>'string','require'=> true,'desc'=> '隐患项目内容'),
            ),
		);
 	}
  
  /**
   * 更新隐患项目
   * #desc 用于更新隐患项目
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患类型是否存在
        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $typeInfo = $hiddTypeDomain->getBaseInfo($this->typeId);
        if(! $typeInfo){
            $rs['code'] = 103;
            $rs['msg'] = T('Hidd type not exists');
            return $rs;
        }
        //查看隐患项目是否存在
        $hiddProjectDomain = new Domain_Zhianbao_HiddProject();
        $projectInfo = $hiddProjectDomain->getBaseInfo($this->projectId);
        if(! $projectInfo){
            $rs['code'] = 103;
            $rs['msg'] = T('Hidd type not exists');
            return $rs;
        }


        $data = array(
            'type_id' => $this->typeId,
            'title' => $this->title,
            'content' => $this->content,
            'last_modify' => time()
        );
        $updateRs = $hiddProjectDomain->updateHiddProject($this->projectId,$data);
        if($updateRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}

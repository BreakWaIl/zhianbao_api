<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddProject_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> true,'desc'=> '隐患类型ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '隐患项目标题'),
                     'content' => array('name' => 'content','type'=>'string','require'=> true,'desc'=> '隐患项目内容'),
            ),
		);
 	}
  
  /**
   * 添加隐患项目
   * #desc 用于添加隐患项目
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
//        //检测公司是否存在
//        $companyDomain = new Domain_Zhianbao_Company();
//        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
//        if(! $companyInfo){
//            $rs['code'] = 100;
//            $rs['msg'] = T('Company not exists');
//            return $rs;
//        }
        //检测隐患类型是否存在
        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $typeInfo = $hiddTypeDomain->getBaseInfo($this->typeId);
        if(! $typeInfo){
            $rs['code'] = 103;
            $rs['msg'] = T('Hidd type not exists');
            return $rs;
        }


        $hiddProjectDomain = new Domain_Zhianbao_HiddProject();
        $data = array(
            'regulator_id' => $this->regulatorId,
            'type_id' => $this->typeId,
            'title' => $this->title,
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $addRs = $hiddProjectDomain->addHiddProject($data);
        if(! $addRs){
            $rs['code'] = 102;
            $rs['msg'] = T('Add fail');
            return $rs;
        }
        $rs['hidd_project_id'] = $addRs;
        return $rs;
    }
	
}

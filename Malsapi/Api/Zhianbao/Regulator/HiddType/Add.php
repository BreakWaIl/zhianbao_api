<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddType_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '隐患类型名称'),
            ),
		);
 	}
  
  /**
   * 添加隐患类型
   * #desc 用于添加隐患类型
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
        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $data = array(
            'regulator_id' => $this->regulatorId,
            'name' => $this->name,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $addRs = $hiddTypeDomain->addHiddType($data);
        if(! $addRs){
            $rs['code'] = 102;
            $rs['msg'] = T('Add fail');
            return $rs;
        }
        $rs['hidd_type_id'] = $addRs;
        return $rs;
    }
	
}

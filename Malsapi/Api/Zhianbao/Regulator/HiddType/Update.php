<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddType_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> true,'desc'=> '隐患类型ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '隐患类型名称'),
            ),
		);
 	}
  
  /**
   * 更新隐患类型
   * #desc 用于更新隐患类型
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
        $data = array(
            'name' => $this->name,
            'last_modify' => time()
        );
        $updateRs = $hiddTypeDomain->updateHiddType($this->typeId,$data);
        if($updateRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}

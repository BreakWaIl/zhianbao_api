<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddType_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> true,'desc'=> '隐患类型ID'),
            ),
		);
 	}
  
  /**
   * 删除隐患类型
   * #desc 用于删除隐患类型
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $delRs = $hiddTypeDomain->delHiddType($this->typeId);
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}

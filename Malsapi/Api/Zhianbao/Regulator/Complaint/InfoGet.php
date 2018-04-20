<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Complaint_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'complaintId' => array('name' => 'complaint_id','type'=>'int','require'=> true,'desc'=> '投诉建议ID'),
            ),
        );
    }
  
  /**
     * 获取投诉建议详情
     * #desc 用于获取投诉建议详情
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $complaintDomain = new Domain_Zhianbao_Complaint();
        $complaintInfo = $complaintDomain->getBaseInfo($this->complaintId);
        $rs['info'] = $complaintInfo;

        return $rs;
    }
    
}

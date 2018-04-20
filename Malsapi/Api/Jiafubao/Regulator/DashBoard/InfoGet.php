<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_DashBoard_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
            ),
		);
 	}

  
  /**
     * 获取首页概况
     * #desc 用于获取首页概况
     *  #return int code 操作码，0表示成功
     * #return int companyTotal 企业数量
     * #return int staffTotal 家政员数量
     * #return int qualifiedTotal 正规家政员数量
     * #return int unqualifiedTotal 需要补充证件的家政员数量
     * #return int orderTotal 近一周订单数
     * #return int demandTotal 未接到的需求数
     * #return int closeOrderRatio 平均退单率
     * #return int replaceOrderTotal 平均换单率
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        $dashBoardDomain = new Domain_Jiafubao_DashBoard();
        $info = $dashBoardDomain->getDashBoard($this->regulatorId);
        $rs['info'] = $info;
        return $rs;
    }

}


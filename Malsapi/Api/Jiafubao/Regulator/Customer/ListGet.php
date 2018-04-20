<?php
/**
 * 默认接口服务类
 *
 * @author: Andy
 */
class Api_Jiafubao_Regulator_Customer_ListGet extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '客户姓名'),
                     'mobile' => array('name' => 'search_mobile', 'type' => 'string', 'require' => false, 'desc' => '手机号'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
        ),
        );
    }


    /**
     * 获取家服宝客户列表
     * #desc 用于获取家服宝客户列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo) {
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

        try {
            $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
            $all = $companyCustomerDomain->reguGetAllCustomer($this->regulatorId,$this->name,$this->mobile,$this->page,$this->pageSize);
            $list = $all['list'];
            $total = $all['total'];
            unset($all['total']);
        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }

}

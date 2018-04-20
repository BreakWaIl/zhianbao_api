<?php
/**
 * 默认接口服务类
 *
 * @author: Andy
 */
class Api_Jiafubao_SubAccount_ListGet extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'string', 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
        ),
        );
    }


    /**
     * 获取家服宝子账户列表
     * #desc 用于获取家服宝的子账户列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //获取用户名
        $domain = new Domain_Jiafubao_User();
        $info = $domain->getBaseByUserId($this->userId);
        if( empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        try {

            $all = $domain->getAllSubAccount($this->userId,$this->page,$this->pageSize,$this->orderby);
            if( !$all){
                $rs['list'] = array();
                $rs['total'] = 0;
            }
            if($all['code'] == 0){
                $rs['total'] = $all['total'];
                if(!empty($all['list'])){
                    $all = $domain->getAllrole($all['list']);
                }
                $rs['list'] = $all;
            }else{
                $rs = $all;
            }

        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }

}

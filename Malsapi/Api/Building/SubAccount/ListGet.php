<?php
/**
 * 默认接口服务类
 *
 * @author: Andy
 */
class Api_Building_SubAccount_ListGet extends PhalApi_Api {

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
     * 获取子账户列表
     * #desc 用于获取主账户下的子账户列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $subDomain = new Domain_Building_SubAccount();

        $filter = array('parent_id' => $this->userId);
        $list = $subDomain->getSubUserList($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $subDomain->getSubUserCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;
        return $rs;
    }

}

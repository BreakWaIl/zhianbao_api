<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Yuyue_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'hotel' => array('name' => 'hotel', 'type' => 'enum','range'=>array('HSBG','SHDS','SHHT','WGDJ','JJSHBG','SHJGBG','SHDFLZBG','SHHBS','XDYKLJ','AZH','SHXFYLY'), 'require' => false, 'desc' => '酒店'),
                     'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => false, 'desc' => '预约人手机'),
                     'jzMobile' => array('name' => 'jz_mobile', 'type' => 'string', 'require' => false, 'desc' => '家政公司手机'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

  /**
     * 获取预约列表
     * #desc 用于获取合作人列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $filter = array();
        $domain = new Domain_Jiafubao_Yuyue();
        if(isset($this->mobile)){
            $filter['mobile'] = $this->mobile;
        }
        if(isset($this->jzMobile)){
            $filter['jz_mobile'] = $this->jzMobile;
        }
        if(isset($this->hotel)){
            $filter['hotel'] = $this->hotel;
        }
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }

}

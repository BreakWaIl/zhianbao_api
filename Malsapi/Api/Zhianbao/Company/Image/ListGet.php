<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Image_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'imgCatId' => array('name' => 'img_cat_id', 'type' => 'int', 'default' => 0, 'require' => true,  'desc' => '分组ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
	
  
  /**
     * 获取图片列表
     * #desc 用于获取图片列表
     * #return int code 操作码，0表示成功
     * #return object list 图片
     * #return int id 品牌ID
     * #return string img_url 图片地址
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['is_del'] = 'n';
        $filter['img_cat_id'] = $this->imgCatId;

        $domain = new Domain_Zhianbao_Image();
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domain->getCount($filter);
        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}

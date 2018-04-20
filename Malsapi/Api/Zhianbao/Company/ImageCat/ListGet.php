<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_ImageCat_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
            ),
		);
 	}
	
  
  /**
     * 获取图片分类列表
     * #desc 用于获取图片分类列表
     * #return int code 操作码，0表示成功
     * #return int id 分组ID
     * #return string name 分组名称
     * #return int name 分组名称
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

        $domain = new Domain_Zhianbao_ImageCat();
        $list = $domain->getCatList($this->companyId);
        $rs['list'] = $list;

        return $rs;
    }
	
}

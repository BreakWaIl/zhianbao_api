<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_User_ListGet extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id', 'type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '监管者ID'),
//                'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '公司名称'),
//                'mobile' => array('name' => 'mobile','type'=>'string','require'=> false,'desc'=> '用户名'),
                'isRegu' => array('name' => 'is_regu','type' => 'enum','range'=>array('y','n'), 'default'=>'y','require' => true, 'desc' => '是否监管：y 是 n 否'),
                'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
        );
    }

    /**
     * 获取公司列表
     * #desc 用于获取公司列表
     * #return int code 操作码，0表示成功
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
        $userDomain = new Domain_Jiafubao_User();
        if($this->isRegu == 'y'){
            //获取已监管的公司
            $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
            if(empty($companyIds)){
                $rs['count'] = 0;
                $rs['list'] = array();
                return $rs;
            }
        }
        if($this->isRegu == 'n'){
            //获取未监管的公司
            $companyIds = $userDomain->getCompanyIds($this->regulatorId);
            if(empty($companyIds)){
                $rs['count'] = 0;
                $rs['list'] = array();
                return $rs;
            }
        }

        $filter = array('id' => $companyIds, 'source' => 'jfb');
        $list = $userDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $userDomain->getCount($filter);
        $rs['regulatorInfo'] = $regulatorInfo;
        $rs['count'] = $count;
        $rs['list'] = $list;

        return $rs;
    }

}

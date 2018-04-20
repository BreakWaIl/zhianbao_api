<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Yuyue_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'name' => array('name' => 'name', 'type' => 'string', 'require' => true, 'desc' => '预约人姓名'),
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '预约人手机'),
                'address' => array('name' => 'address', 'type' => 'string', 'require' => true, 'desc' => '家庭住址'),
                'hotel' => array('name' => 'hotel', 'type' => 'enum','range'=>array('HSBG','SHDS','SHHT','WGDJ','JJSHBG','SHJGBG','SHDFLZBG','SHHBS','XDYKLJ','AZH','SHXFYLY'), 'require' => true, 'desc' => '酒店'),
                'homeType' => array('name' => 'home_type', 'type' => 'string', 'require' => true, 'desc' => '房型'),
                'birthday' => array('name' => 'birthday', 'type' => 'string', 'require' => true, 'desc' => '老人生日'),
                'beginTime' => array('name' => 'begin_time', 'type' => 'string', 'require' => true, 'desc' => '开始时间'),
                'endTime' => array('name' => 'end_time', 'type' => 'string', 'require' => true, 'desc' => '结束时间'),
                'homeNum' => array('name' => 'home_num', 'type' => 'int', 'require' => true, 'desc' => '订房数'),
                'health' => array('name' => 'health', 'type' => 'string', 'require' => true, 'desc' => '老人健康情况'),
                'jzCompanyName' => array('name' => 'jz_company_name', 'type' => 'string', 'require' => true, 'desc' => '服务家政机构名称'),
                'jzName' => array('name' => 'jz_name', 'type' => 'string', 'require' => true, 'desc' => '服务家政机构联系人'),
                'jzMobile' => array('name' => 'jz_mobile', 'type' => 'string', 'require' => true, 'desc' => '家政公司手机'),
                'jzAddress' => array('name' => 'jz_address', 'type' => 'string', 'require' => true, 'desc' => '服务家政机构地址'),
                'source' => array('name' => 'source', 'type' => 'enum', 'range' => array('personal','vip'),'require' => true, 'desc' => '预约来源'),
            ),
        );
    }


    /**
     * 添加预约
     * #desc 用于添加预约
     * #return int code 操作码，0表示成功
     * #return int id 预约ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_Yuyue();
        $data = array(
            'name' => $this->name,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'hotel' => $this->hotel,
            'home_type' => $this->homeType,
            'birthday' => strtotime($this->birthday),
            'begin_time' => strtotime($this->beginTime),
            'end_time' => strtotime($this->endTime),
            'home_num' => $this->homeNum,
            'health' => $this->health,
            'jz_company_name' => $this->jzCompanyName,
            'jz_name' => $this->jzName,
            'jz_mobile' => $this->jzMobile,
            'jz_address' => $this->jzAddress,
            'source' => $this->source,
            'create_time' => time(),
            'last_modify' => time()
        );
        switch ($this->hotel){
            case 'HSBG' : $data['hotel_name'] = '衡山宾馆';break;
            case 'SHDS' : $data['hotel_name'] = '上海大厦';break;
            case 'SHHT' : $data['hotel_name'] = '上海华亭';break;
            case 'WGDJ' : $data['hotel_name'] = '吴宫大酒店';break;
            case 'JJSHBG' : $data['hotel_name'] = '锦江上海宾馆';break;
            case 'SHJGBG' : $data['hotel_name'] = '上海建国宾馆';break;
            case 'SHDFLZBG' : $data['hotel_name'] = '衡山宾馆';break;
            case 'SHHBS' : $data['hotel_name'] = '上海好帮手社区服务发展中心';break;
            case 'XDYKLJ' : $data['hotel_name'] = '新东苑快乐家园';break;
            case 'AZH' : $data['hotel_name'] = '爱照护养老服务有限公司';break;
            case 'SHXFYLY' : $data['hotel_name'] = '上海遐福养老院';break;
        }
        //判断是否可以预约
        $canYuyue = $domain->canYuyue($this->hotel);
        if(! $canYuyue){
            $rs['code'] = 100;
            $rs['msg'] = T('This hotel  is full today,Please come again tomorrow');
            return $rs;
        }
        $id = $domain->addYuyue($data);
        $rs['info']['id'] = $id;
        return $rs;
    }

}

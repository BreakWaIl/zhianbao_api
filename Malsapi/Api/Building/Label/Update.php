<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Label_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'labelId' => array('name' => 'label_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '标签ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '标签名称'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  
  /**
     * 更新标签
     * #desc 用于更新标签
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断标签是否存在
        $labelDomain = new Domain_Building_Label();
        $labelInfo = $labelDomain->getBaseInfo($this->labelId);
        if (empty($labelInfo)) {
            $rs['code'] = 203;
            $rs['msg'] = T('Label not exists');
            return $rs;
        }
        //查询标签名称是否存在
        $labelDomain = new Domain_Building_Label();
        $catInfo = $labelDomain->getBaseInfoByName($this->companyId,$this->name);
        if (! empty($catInfo)) {
            DI()->logger->debug('Name exists', $this->name);

            $rs['code'] = 107;
            $rs['msg'] = T('Name exists');
            return $rs;
        }
        $data = array(
            'label_id' => $this->labelId,
            'name' => $this->name,
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $res = $labelDomain->update($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}


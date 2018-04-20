<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Label_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '标签名称'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加标签
     * #desc 用于添加标签
     * #return int code 操作码，0表示成功
     * #return int label_id 标签ID
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
            'company_id' => $this->companyId,
            'name' => $this->name,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $labelId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $labelId = $labelDomain->add($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['label_id'] = $labelId;

        return $rs;
    }

}

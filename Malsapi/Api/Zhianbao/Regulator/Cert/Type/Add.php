<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Cert_Type_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'partId' => array('name' => 'part_id', 'type' => 'string', 'require' => true, 'desc' => '角色ID,多个用“,”隔开'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '证书名称'),
            ),
        );
    }


    /**
     * 添加员工证书类型
     * #desc 用于添加员工证书类型
     * #return int code 操作码，0表示成功
     * #return int type_id  类型ID
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

        $data = array(
            'regulator_id' => $this->regulatorId,
            'part_id' => $this->partId,
            'name' => $this->name,
            'create_time' => time(),
            'last_modify' => time(),
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $typeDomain = new Domain_Zhianbao_CertType();
            $id = $typeDomain->addCertType($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['info']['template_id'] = $id;

        return $rs;
    }

}

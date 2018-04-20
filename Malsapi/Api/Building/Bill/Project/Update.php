<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Project_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'billId' => array('name' => 'bill_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '账单ID'),
//                'type' => array('name' => 'type', 'type'=>'enum','range' => array('expenditure','income','borrow'), 'default' => 'expenditure', 'require'=> true,'desc'=> '类型：expenditure 支出, income 收入 borrow 借支'),
                'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '出入账标题'),
                'amount' => array('name' => 'amount', 'type' => 'float', 'require' => true, 'desc' => '出入金额'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 更新项目出入账单
     * #desc 用于更新项目出入账单
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断账单是否存在
        $billProjectDomain = new Domain_Building_BillProject();
        $billInfo = $billProjectDomain->getBaseInfo($this->billId);
        if (empty($billInfo)) {
            $rs['code'] = 206;
            $rs['msg'] = T('Bill not exists');
            return $rs;
        }
        //判断公司项目是否存在
        $projectDomain = new Domain_Building_Project();
        $projectInfo = $projectDomain->getBaseInfo($billInfo['project_id']);
        if (empty($projectInfo)) {
            $rs['code'] = 192;
            $rs['msg'] = T('Project not exists');
            return $rs;
        }
        $data = array(
            'bill_id' => $this->billId,
            'title' => $this->title,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );

        $billProjectDomain = new Domain_Building_BillProject();
        $res = true;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $billProjectDomain->update($data,$billInfo,$projectInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}

<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeSelf_Review_FirstReject extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '申请ID'),
                'rejectRemark' => array('name' => 'reject_remark','type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '拒绝理由'),
            ),
        );
    }


    /**
     * 初审拒绝申报
     * #desc 用于初审拒绝申报
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //判断申请是否存在
        $applyDomain = new Domain_Zhianbao_SafeApply();
        $applyInfo = $applyDomain->getReviewInfo($this->regulatorId,$this->applyId);
        if(! $applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        if($applyInfo['status'] != 'review'){
            DI()->logger->debug('Apply failed', $this->applyId);

            $rs['code'] = 116;
            $rs['msg'] = T('Apply failed');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyDomain = new Domain_Zhianbao_SafeApply();
            $res = $applyDomain->firstReject($applyInfo,$this->rejectRemark);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
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

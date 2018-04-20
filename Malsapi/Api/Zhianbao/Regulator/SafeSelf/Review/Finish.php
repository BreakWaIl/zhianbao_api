<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeSelf_Review_Finish extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '申请ID'),
                'mechanism' => array('name' => 'mechanism','type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '发证机构'),
                'issueTime' => array('name' => 'issue_time','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '发证日期'),
                'endTime' => array('name' => 'end_time','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '截至有效期'),
                'certBn' => array('name' => 'cert_bn','type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '证书编号'),
                'reviewRemark' => array('name' => 'review_remark','type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '复审理由'),
            ),
        );
    }


    /**
     * 初审生产安全申报
     * #desc 用于初审生产安全申报
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
        if($applyInfo['status'] != 'firstReview'){
            DI()->logger->debug('Apply failed', $this->applyId);

            $rs['code'] = 116;
            $rs['msg'] = T('Apply failed');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyDomain = new Domain_Zhianbao_SafeApply();
            $data = array(
                'mechanism' => $this->mechanism,
                'issue_time' => $this->issueTime,
                'end_time' => $this->endTime,
                'end_review_remark' => $this->reviewRemark,
                'cert_bn' => $this->certBn,
            );
            $res = $applyDomain->finishReview($applyInfo,$data);
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

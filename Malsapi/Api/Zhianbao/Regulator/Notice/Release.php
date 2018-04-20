<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Notice_Release extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'noticeId' => array('name' => 'notice_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '通知ID'),
            ),
        );
    }


    /**
     * 发布通知
     * #desc 用于发布通知
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go(){
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

        //判断通知是否存在
        $domainNotice = new Domain_Zhianbao_Notice();
        $noticeInfo = $domainNotice->getBaseInfo($this->noticeId);
        if( !$noticeInfo) {
            DI()->logger->debug('Notice not found', $this->noticeId);

            $rs['code'] = 101;
            $rs['msg'] = T('Notice not exists');
            return $rs;
        }
        //判断是否发布
        if($noticeInfo['is_release'] == 'y'){
            DI()->logger->debug('Notice have been release', $this->noticeId);

            $rs['code'] = 109;
            $rs['msg'] = T('Notice have been release');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $info = $domainNotice->release($this->regulatorId, $this->noticeId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if( $info){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}

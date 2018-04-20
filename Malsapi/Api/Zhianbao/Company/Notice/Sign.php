<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Notice_Sign extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'noticeId' => array('name' => 'notice_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '通知ID'),
            ),
        );
    }
  
  /**
     * 签收发文通知
     * #desc 用于签收发文通知
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功 1 失败
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        //判断通知是否存在
        $domainNotice = new Domain_Zhianbao_Notice();
        $noticeInfo = $domainNotice->getNoticeInfo($this->noticeId,$this->companyId);
        if( !$noticeInfo) {
            DI()->logger->debug('Notice not found', $this->noticeId);

            $rs['code'] = 101;
            $rs['msg'] = T('Notice not exists');
            return $rs;
        }
        $signInfo = $domainNotice->SignInfo($this->noticeId,$this->companyId);
        if($signInfo['is_sign'] == 'y') {
            DI()->logger->debug('Notice have been sign', $this->noticeId);

            $rs['code'] = 130;
            $rs['msg'] = T('Notice have been sign');
            return $rs;
        }
        $res = $domainNotice->Sign($this->noticeId,$this->companyId);
        if($res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['status'] = $status;

        return $rs;
    }
    
}

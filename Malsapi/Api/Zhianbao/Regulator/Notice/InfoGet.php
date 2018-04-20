<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Notice_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'noticeId' => array('name' => 'notice_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '通知ID'),
            ),
        );
    }
  
  /**
     * 获取发文通知
     * #desc 用于获取当前发文通知信息
     * #return int code 操作码，0表示成功
     * #return int id 通知ID
     * #return int regulator_id 监管者ID
     * #return string title 通知标题
     * #return string content 通知内容
     * #return string is_release 是否发布：y 已发布 n 未发布
     * #return int create_time 创建时间
     * #return int last_modify 最后更新时间
     * #return int release_time 发布时间
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

        //判断通知是否存在
        $domainNotice = new Domain_Zhianbao_Notice();
        $noticeInfo = $domainNotice->getBaseInfo($this->noticeId);
        if( !$noticeInfo) {
            DI()->logger->debug('Notice not found', $this->noticeId);

            $rs['code'] = 101;
            $rs['msg'] = T('Notice not exists');
            return $rs;
        }

        $rs['info'] = $noticeInfo;

        return $rs;
    }
    
}

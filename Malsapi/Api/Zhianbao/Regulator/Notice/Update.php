<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Regulator_Notice_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'noticeId' => array('name' => 'notice_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '通知ID'),
                     'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '通知标题'),
                     'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '通知内容'),
            ),
		);
 	}
	
  
  /**
     * 更新通知信息
     * #desc 用于更新通知信息
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
        }

        if($noticeInfo['title'] != $this->title){
            //判断标题是否重复
            $domainNotice = new Domain_Zhianbao_Notice();
            $res = $domainNotice->getTitle($this->regulatorId,$this->title);
            if (!empty($res)) {
                DI()->logger->debug('Name exists', $this->title);

                $rs['code'] = 107;
                $rs['msg'] = T('Name exists');
                return $rs;
            }
        }

        $data = array(
            'notice_id' => $this->noticeId,
            'title' => $this->title,
            'content' => $this->content,
            'last_modify' => time(),
        );

        $info = $domainNotice->updateNotice($data);
        if( $info){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}

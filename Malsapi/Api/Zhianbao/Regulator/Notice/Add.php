<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Notice_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '通知标题'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '通知内容'),
            ),
        );
    }


    /**
     * 添加发文通知
     * #desc 用于添加发文通知
     * #return int code 操作码，0表示成功
     * #return int notice_id  通知ID
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

        //判断标题是否重复
        $domainNotice = new Domain_Zhianbao_Notice();
        $res = $domainNotice->getTitle($this->regulatorId,$this->title);
        if (!empty($res)) {
            DI()->logger->debug('Name exists', $this->title);

            $rs['code'] = 107;
            $rs['msg'] = T('Name exists');
            return $rs;
        }

        $data = array(
            'regulator_id' => $this->regulatorId,
            'title' => $this->title,
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $noticeId = $domainNotice->addNotice($data);

        $rs['info']['notice_id'] = $noticeId;

        return $rs;
    }

}

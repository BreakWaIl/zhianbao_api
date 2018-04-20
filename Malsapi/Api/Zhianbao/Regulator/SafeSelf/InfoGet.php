<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeSelf_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'applyId' => array('name' => 'apply_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '申请ID'),
            ),
		);
 	}

  
  /**
     * 获取申报安全生产申报详情
     * #desc 用于获取申报安全生产申报详情
     * #return int id 申报ID
     * #return int company_id 公司ID
     * #return int user_id 申报人ID
     * #return string company_name 公司名称
     * #return string user_name 申报人名称
     * #return string apply_title 申报标题
     * #return int template_id 模板ID
     * #return string self_content 申报内容
     * #return int apply_grade 申报等级
     * #return int apply_time 申报时间
     * #return int review_time 审核时间
     * #return string apply_theway 申报方式
     * #return string file_path 文件路径
     * #return string status 状态：wait 等待 applying 申请中 review 审核中 firstReview 初审 finish 审核完成
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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

        $rs['info'] = $applyInfo;
        return $rs;
    }

}


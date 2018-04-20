<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeSelf_Grade_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
            ),
		);
 	}

  
  /**
     * 获取安全生产标准化公司等级
     * #desc 用于获取安全生产标准化公司等级
   * #return int id 等级ID
   * #return int apply_id 申报ID
   * #return int company_id 公司ID
   * #return int apply_grade 公司等级
   * #return string mechanism 发证机构
   * #return int issue_time 发证日期
   * #return int complete_time  达标时间
   * #return int create_time 创建时间
   * #return int end_time  截止有效期
   * #return int apply_time 申报时间
   * #return int next_apply_time 下次申请日期
   * #return string status firstReview 初审完成, finish 复核完成
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

        $gradeDomain = new Domain_Zhianbao_SafeGrade();
        $gradeInfo = $gradeDomain->getBaseInfo($this->companyId,$companyInfo);

        $rs['info'] = $gradeInfo;
        return $rs;
    }

}


<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_SendHouseStaff_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家服云公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
            ),
		);
 	}
	
  
  /**
     * 删除共享的家政员
     * #desc 用于删除其他公司给自己共享的家政员
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_CompanyShareHouseStaff();
        $filter = array(
            'company_id' => $this->jfbCompanyId,
            'staff_id' => $this->staffId
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $domain->deleteShareStaff($filter);
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

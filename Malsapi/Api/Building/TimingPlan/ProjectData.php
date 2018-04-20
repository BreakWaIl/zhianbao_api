<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_TimingPlan_ProjectData extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
            ),
		);
 	}

  /**
     * 统计当前公司下项目信息
     * #desc 用于统计当前公司下项目信息
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $statisticsDomain = new Domain_Building_Statistics();
            $statisticsDomain->projectData();
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }
	
}

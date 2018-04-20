<?php
/**
 *
 * 神铺MQ调度
 *
 */


require_once dirname(__FILE__) . '/init.php';

//装载你的接口
DI()->loader->addDirs(array('Malsapi','Library','Library/Task/Task'));

try {
    $mq = new Task_MQ_DB();
    $progress = new Task_Runner_Dispatch($mq);
    $progress->go();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}

<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/1
 * Time: 9:59
 */
require_once(  'auth.php');
require_once(  'conf.php');
require_once(  'cosapi.php');
require_once(  'http_client.php');
require_once(  'slice_uploading.php');
class Qcloudcos_Qcloud
{
    function __construct()
    {
    }

    function uploadImg($url,$type)
    {
        $dst = time() . rand(1000, 9999) . '.' . $type;
        $ret = Cosapi::upload('51suyuan',  $url, $dst);
        if ($ret['code'] == 0 || $ret['message'] == 'SUCCESS') {
            return 'http://img.mshenpu.cc/' . $dst;
        } else {
            return NULL;
        }
    }
    function deleteImg($url){
        $file_name = str_replace('http://img.mshenpu.cc','',$url);
        $ret = Cosapi::delFile('51suyuan', $file_name);
        if ($ret['code'] == 0 || $ret['message'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}
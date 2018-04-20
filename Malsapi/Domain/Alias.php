<?php


class Domain_Alias {

    // 别名服务，可以生成 A-Z的26个字母 + 0-1的10个数字随机的alias串
    public function genAlias($length = 10) {
        $alias = '';
        $strings = "0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strings)-1;
        for( $i=0; $i<$length; $i++ ){
            $alias .= $strings[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $alias;
    }
}

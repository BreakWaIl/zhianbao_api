<?php

interface Wechat_Plugin_Text
{
    public function process($inMessage, &$outMessage);
}

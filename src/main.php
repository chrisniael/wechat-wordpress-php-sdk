<?php
/**
 * 微信公众平台对接WordPress PHP SDK
 *
 * @author     沈煜 <chrisniael@vip.qq.com>
 * @license    MIT License
 */
 
// 两种模式中选其一
require("wechat.php");	//自定义菜单模式
//require("wechat_normal.php");		//非自定义菜单模式

$wechatObj = new WechatWordpress();
//$wechatObj->valid();	//仅验证使用
$wechatObj->responseMsg();

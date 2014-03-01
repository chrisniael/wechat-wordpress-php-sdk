<?php
/**
 * 微信公众平台对接WordPress PHP SDK
 *
 * @author     沈煜 <chrisniael@vip.qq.com>
 * @license    MIT License
 */
 
header("Content-type: text/html; charset=utf-8"); 

$appID = "****";	//替换成你的appID
$appSecret = "****";	//替换成你的appSecret

function doCurlGetRquest($url, $data, $timeout = 5)
{
    if($url == "" || $timeout <= 0)
    {
        return false;
    }
    
    $url = $url . '?' . http_build_query($data);
    
    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
    
    return curl_exec($con);
}

function getToken($appID, $appSecret)
{
    $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token";
    //echo $getTokenUrl;
    
    $get_data_array = array(
        "grant_type" => "client_credential",
        "appid" => $appID,
        "secret" => $appSecret
        );
    
    //echo http_build_query($get_data_array);
    
    $json_result =  doCurlGetRquest($getTokenUrl, $get_data_array);
    //echo $json_result;
    
    $json_array = json_decode($json_result);
    //var_dump($json_array);
    
    //appsecret 错误
    if($json_array->errcode !== NULL)
    {
        return false;
    }
    
    //var_dump($json_array->access_token);
    return $json_array->access_token;
}

$access_token = getToken($appID, $appSecret);
//echo $access_token;
//echo "<br />";


function doCurlPostRequest($url, $requestString, $timeout = 5)
{
    if($url == "" || $requestString == "" || $timeout == "")
    {
        return false;
    }
    
    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
    curl_setopt($con, CURLOPT_POST, true);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
    
    return curl_exec($con);
}


//自定义菜单
$menu_array = array(
	"button" => array(
		array(
			"type" => "view",
			"name" => "主页",
			"url" => "http://www.xn--guwy6h.cn"
		),
		array(
			"name" => "文章",
			"sub_button" => array(
				array(
					"type" => "click",
					"name" => "近期文章",
					"key" => "VK_RECENT"
				),
				array(
					"type" => "click",
					"name" => "文章归档",
					"key" => "VK_DATE"
				),
				array(
					"type" => "click",
					"name" => "分类目录",
					"key" => "VK_CATEGORY"
				),
				array(
					"type" => "click",
					"name" => "标签列表",
					"key" => "VK_TAG"
				)
			)
		),

		array(
			"name" => "其它",
			"sub_button" => array(
				array(
					"type" => "view",
					"name" => "书籍推荐",
					"url" => "http://www.xn--guwy6h.cn/?page_id=6"
				),
				array(
					"type" => "view",
					"name" => "网站链接",
					"url" => "http://www.xn--guwy6h.cn/?page_id=11"
				),
				array(
					"type" => "view",
					"name" => "留言小本",
					"url" => "http://www.xn--guwy6h.cn/?page_id=9"
				),
				array(
					"type" => "view",
					"name" => "关于本站",
					"url" => "http://www.xn--guwy6h.cn/?page_id=5"
				)
			)
		)
	)
);


function array_urlencode(&$array)
{
	foreach($array as $key => &$value)
	{
		if(is_array($value))
		{
			array_urlencode($value);
		}
		else
		{
			$value = urlencode($value);
			//echo $value . "<br />";
		}
	}
}


// 生成自定义菜单按钮
function create_menu($access_token, $menu_array)
{
	array_urlencode($menu_array);
	//var_dump($menu_array);

	//json_encode中文会乱码，需要先使用urlencode，然后对json_encode的返回值进行urldecode
	$menu_json = urldecode(json_encode($menu_array));
    //echo $menu_json;
    
    $create_menu_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
    $json_result = doCurlPostRequest($create_menu_url, $menu_json);
    //var_dump($json_result);
    
    
    $json_array = json_decode($json_result);
    var_dump($json_array);
}

create_menu($access_token, $menu_array);



// 删除自定义菜单按钮
function delete_menu($access_token)
{
	$delete_menu_url = "https://api.weixin.qq.com/cgi-bin/menu/delete";	
	
	$get_data_array = array(
		"access_token" => $access_token
	);
	
	$json_result =  doCurlGetRquest($delete_menu_url, $get_data_array);
	var_dump($json_result);
}

//delete_menu($access_token);


?>

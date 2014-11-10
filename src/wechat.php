<?php
/**
 * 微信公众平台对接WordPress PHP SDK
 *
 * @author     沈煜 <shenyu@shenyu.me>
 * @license    MIT License
 */
 
//Token
define("TOKEN", "shenyu");
                  
//数据库信息
define('MYSQL_HOST', '****');	//数据库主机地址
define('MYSQL_USER', '****');	//数据库用户名
define('MYSQL_PASSWORD', '****');	//数据库密码
define('MYSQL_DB', '****');		//WordPress数据库名

//博客的主页地址
define("SITE", "http://www.xn--guwy6h.cn");		//替换成你的WordPress博客地址


//数据库查询
class SQL
{
	function __construct()
	{
		$this->db = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
						                    
	    if($this->db->connect_error)
	    {
	    	$this->isConnected = false;
		}
		else
		{
			$this->isConnected = true;	
		}
	}
	
	function getResult($query)
	{
	    $this->db->query("set names 'utf8'");
	        
	    $this->result = $this->db->query($query);
		if($this->result == 0)
		{
			$this->hasResult = false;
		}
		else
		{
			$this->hasResult = true;
		}		
		
		return $this->result;
	}
	
	function __destruct()
	{
		if($this->hasResult)
	        $this->result->free();
		
		if($this->isConnected)
	        $this->db->close();
	}
	
	private $db;
	private $isConnected;
	private $hasResult;
	private $result;
}

class WechatWordpress
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //检测Signature是否正确
        if($this->checkSignature())
        {
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr))
        {            
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgType = $postObj->MsgType;
            
            $time = time();
            
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            
            if($msgType == "text")
            {
            	/*
                $keyword = trim($postObj->Content);
				
				
                $msgType = "text";
                
                $contentStr = $keyword;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
				 * 
				 */
            }
            else if($msgType == "event")
            {
                $event = $postObj->Event;
                if($event == "subscribe")
                {
                    $msgType = "text";
                    
                    $contentStr = "你好，欢迎你的关注！";
                    
                    
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
				else if($event == "CLICK")
				{
					$eventKey = $postObj->EventKey;
					
					if($eventKey == "VK_RECENT")
	                {   
	                                        
	                    $query = "select `id`, `post_title`, `post_content`
	                              from `wp_posts`
	                              where `post_status` like 'publish' and `post_type` like 'post'
	                              order by `post_date` desc
	                              limit 0, 5";
									  
						$sql = new SQL();
	                    $result = $sql->getResult($query);
						
						if($result === FALSE)
						{
							$msgType = "text";                    
	                    
		                    $contentStr = "服务器维护中";
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}
						else
						{
		                    $strTitle = "近期文章：\n\n";
		                    $num_results = $result->num_rows;
		                    for($i = 0; $i < $num_results; ++$i)
		                    {
		                        $row = $result->fetch_assoc();
		                        
		                        $strTitle .= '「<a href="'. SITE . '/?p=' . $row['id'] . '">';
		                        $strTitle .= $row['post_title'];
		                        $strTitle .= "</a>」\n\n";
		                    }
		                    
		                    $msgType = "text";
		                    
		                    
		                    $contentStr = $strTitle;
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;						
						}
	
	                        
	                }
	                else if($eventKey == "VK_DATE")
	                {
	                    $query = "select year(`post_date`), month(`post_date`), extract(year_month from(`post_date`)), count(extract(year_month from(`post_date`))) 
	                                    from `wp_posts`
	                                    where `post_status` like 'publish' and `post_type` like 'post' 
	                                    group by extract(year_month from(`post_date`))
	                                    order by extract(year_month from(`post_date`)) desc";
										
										
						$sql = new SQL();
	                    $result = $sql->getResult($query);
						
						if($result === FALSE)
						{
							$msgType = "text";                    
	                    
		                    $contentStr = "服务器维护中";
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}
						else
						{
		                    $strTitle = "文章归档：\n\n";
		                    $num_results = $result->num_rows;
		                    for($i = 0; $i < $num_results; ++$i)
		                    {
		                        $row = $result->fetch_assoc();
		                        
		                        $strTitle .= '  <a href="'. SITE . '/?m=' . $row['extract(year_month from(`post_date`))'] . '">';
		                        $strTitle .= $row['year(`post_date`)'] . "年".
		                            chineseMonth($row['month(`post_date`)']) . "月";
		                        
		                        $strTitle .= "</a> (" .$row['count(extract(year_month from(`post_date`)))']. ")\n\n";
		                    }
		                    
		                    $msgType = "text";
		                    
		                    
		                    $contentStr = $strTitle;
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
	                	}
	                        
	                }
	                else if($eventKey == "VK_CATEGORY")
	                {
	                	$query = "select `wp_terms`.`term_id`, `wp_terms`.`name`, `wp_term_taxonomy`.`count`
	                                from `wp_terms`, `wp_term_taxonomy`
	                                where `wp_term_taxonomy`.`taxonomy` like 'category' and `wp_term_taxonomy`.`count` != 0
	                                and `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id`";
	                        
	                        
	                    $sql = new SQL();
	                    $result = $sql->getResult($query);
						
						if($result === FALSE)
						{
							$msgType = "text";                    
	                    
		                    $contentStr = "服务器维护中";
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}
						else
						{
		                    $strTitle = "分类目录：\n\n";
		                    $num_results = $result->num_rows;
		                    for($i = 0; $i < $num_results; ++$i)
		                    {
		                        $row = $result->fetch_assoc();
		                        
		                        $strTitle .= '  <a href="'. SITE . '/?cat=' . $row['term_id'] . '">';
		                        $strTitle .= $row['name'];
		                        
		                        $strTitle .= "</a> (" .$row['count']. ")\n\n";
		                    }
		                    
		                    $msgType = "text";
		                    
		                    $contentStr = $strTitle;
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}                        
	                }
	                else if($eventKey == "VK_TAG")
	                {
	                    $query = "select `wp_terms`.`slug`, `wp_terms`.`name`, `wp_term_taxonomy`.`count`
	                                from `wp_terms`, `wp_term_taxonomy`
	                                where `wp_term_taxonomy`.`taxonomy` like 'post_tag' and `wp_term_taxonomy`.`count` != 0
	                                and `wp_terms`.`term_id` = `wp_term_taxonomy`.`term_id`";
	                        
	                        
	                    $sql = new SQL();
	                    $result = $sql->getResult($query);
						
						if($result === FALSE)
						{
							$msgType = "text";                    
	                    
		                    $contentStr = "服务器维护中";
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}
						else
						{
		                    $strTitle = "标签列表：\n\n";
		                    $num_results = $result->num_rows;
		                    for($i = 0; $i < $num_results; ++$i)
		                    {
		                        $row = $result->fetch_assoc();
		                        
		                        $strTitle .= '  <a href="'. SITE . '/?tag=' . $row['slug'] . '">';
		                        $strTitle .= $row['name'];
		                        
		                        $strTitle .= "</a> (" .$row['count']. ")\n\n";
		                    }
		                    
		                    $msgType = "text";
		                    
		                    $contentStr = $strTitle;
		                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
		                    echo $resultStr;
						}
	                }					
				}
            }

        }
        else
        {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature )
        {
			return true;
		}else
        {
			return false;
		}
	}
}

function chineseMonth($month_num)
{
    $month_ch;
    switch($month_num)
    {
        case 1:
        $month_ch = "一";
        break;
        
        case 2:
        $month_ch = "二";
        break;
        
        case 3:
        $month_ch = "三";
        break;
        
        case 4:
        $month_ch = "四";
        break;
        
        case 5:
        $month_ch = "五";
        break;
        
        case 6:
        $month_ch = "六";
        break;
        
        case 7:
        $month_ch = "七";
        break;
        
        case 8:
        $month_ch = "八";
        break;
        
        case 9:
        $month_ch = "九";
        break;
        
        case 10:
        $month_ch = "十";
        break;
        
        case 11:
        $month_ch = "十一";
        break;
        
        case 12:
        $month_ch = "十二";
        break;
        
        default:
        $month_ch = "一";
    }
    
    return $month_ch;
}
?>

<?php    
    include_once 'aliyun-php-sdk-core/Config.php';
    use Dm\Request\V20151123 as Dm; 
	/**
	 * 调用阿里邮件推送SDK
	 * 传入参数：目标地址，邮件主题，邮件html正文
	 */
	function sendMail($toAddress,$subject,$htmlBody){
		$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "XmTB0eVhMOYO950D", "Ffc7gO1hDAqQ0MENIGgGctWFI2HwoT");        
	    $client = new DefaultAcsClient($iClientProfile);    
	    $request = new Dm\SingleSendMailRequest();     
	    $request->setAccountName("autoreply@notice.icb2017.org");
	    $request->setFromAlias("ICB2017");
	    $request->setAddressType(1);
	    $request->setTagName("submission");
	    $request->setReplyToAddress("true");
	    $request->setToAddress($toAddress);        
	    $request->setSubject($subject);
	    $request->setHtmlBody($htmlBody);        
	    $response = $client->getAcsResponse($request);    
	    return $response;
	}  
	
	
	/*
	 * 重命名上传文件
	 * 传入参数：主题序号，名，姓
	 * 返回格式：Feed_Kong.W_1464616722.doc
	 */
    function getFileName($topic,$first_name,$last_name){
    	$topic_arr = array(
			"1"	=> "Feed",
			"2"	=> "Bio-based",
			"3"	=> "Pretreatment",
			"4"	=> "Synthetic",
			"5"	=> "Conversion",
			"6"	=> "Integrated",
			"7"	=> "Others"
		);
		$file_name = $topic_arr["$topic"]."_".ucfirst($last_name).".".substr(ucfirst($first_name), 0, 1)."_".time();
		
		return str_replace(" ","_",$file_name);
    }         
        

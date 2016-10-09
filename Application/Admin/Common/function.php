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
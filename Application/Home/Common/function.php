<?php    
    include_once 'aliyun-php-sdk-core/Config.php';
    use Dm\Request\V20151123 as Dm; 
	function sendMail(){
		$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "XmTB0eVhMOYO950D", "Ffc7gO1hDAqQ0MENIGgGctWFI2HwoT");        
	    $client = new DefaultAcsClient($iClientProfile);    
	    $request = new Dm\SingleSendMailRequest();     
	    $request->setAccountName("autoreply@notice.icb2017.org");
	    $request->setFromAlias("ICB2017");
	    $request->setAddressType(1);
	    $request->setTagName("submission");
	    $request->setReplyToAddress("true");
	    $request->setToAddress("807729560@163.com");        
	    $request->setSubject("Submission Initiated");
	    $request->setHtmlBody("Your submission for 6th International Conference on Biorefinery (ICB2017) has been initiated.");        
	    $response = $client->getAcsResponse($request);    
	    return $response;
	}           
        

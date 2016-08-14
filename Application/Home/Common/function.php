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
    function getFileName($type,$topic,$first_name,$last_name){
    	$topic_arr = array(
			"1"	=> "Feed",
			"2"	=> "Bio-based",
			"3"	=> "Pretreatment",
			"4"	=> "Synthetic",
			"5"	=> "Conversion",
			"6"	=> "Integrated",
			"7"	=> "Others"
		);
		$type_arr = array(
			"1" => "Poster",
			"2" => "Oral"
		);
		$file_name = $type_arr["$type"]."_".$topic_arr["$topic"]."_".ucfirst($last_name).".".substr(ucfirst($first_name), 0, 1)."_".time();
		
		return str_replace(" ","_",$file_name);
    }         
        
	//生成随机数,用于生成salt
    function random_str($length){
        //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++){
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
        return $str;
    }
	//生成随机数,用于生成密码
    function random_pwd($length){
        //生成一个包含数字的数组
        $arr = array_merge(range(0, 9));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++){
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
        return $str;
    }
	
	//生成编号，格式：ICB201700001
	function gen_refer_no(){
		$Reg = M('Reg');
		$maxRegId = $Reg->max('reg_id');
		if(!$maxRegId){
			$maxRegId = 0;
		}
		$refer_no = "ICB2017".str_pad($maxRegId + 1,5,"0",STR_PAD_LEFT);
		return $refer_no;
		
	}
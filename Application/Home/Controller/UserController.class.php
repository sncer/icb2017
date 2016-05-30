<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
            case 'login':break;
			case 'user_login':break;
            case 'register':break;
            case 'user_register':break;
            case 'verify_email':break;
			case 'register_abstract':break;
			case 'visitor_abstract':break;
            default:parent::_checkLogin();
        }
        
    }
	
	//打开注册页面
	public function register(){
		$this->assign("title_list",C('TITLE_LIST'));
		$this->assign("country_list",C('COUNTRY_LIST'));
		
    	$this->display();
    }
	//用户注册操作
	public function user_register(){
		$title_list = C('TITLE_LIST');
		$country_list = C('COUNTRY_LIST');
		// 获取表单的POST数据
		$data['title'] = $title_list[$_POST['title']];
		$data['first_name'] = trim($_POST['first_name']);
		$data['last_name'] = trim($_POST['last_name']);
		$data['affiliation'] = $_POST['affiliation'];
		$data['country'] = $country_list[$_POST['country']];
		$data['city'] = $_POST['city'];
		$data['address'] = $_POST['address'];
		$data['zip'] = $_POST['zip'];
		$data['email'] = $_POST['email'];
		$password = $_POST['password'];
		$salt = $this->random_str(6);
		$data['password'] = md5(md5($password) . $salt);
		$data['salt'] = $salt;
		//默认值
		//$data['updated_time'] = date("Y-m-d H:i:s");
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['active'] = 1;
		// 实例化User模型
		$User = M('User');
		$res_id = $User->add($data);
		if($res_id > 0){
			//根据id获取用户信息，并存入session，跳转到dashboard
			$result = $User->where("user_id = $res_id")->find();
			session('user',$result);
			$this->assign('user',$result);
			//设置成功后跳转后台主页面的地址   
			$this->success('Register successfully!', U('User/dashboard'),2);
		}else{
			$this->display('Public:500');
		}
	}
	//验证邮箱是否已经存在
	public function verify_email(){
		$email = $_POST['email'];
		// 实例化User模型
		$User = M('User');
		// Check email existence
		$res = $User->where("email = '".$email."'")->find();
		
		if($res){
			$isAvailable = false;
		}else{
			$isAvailable = true;
		}
		// Finally, return a JSON
		echo json_encode(array(
		    'valid' => $isAvailable,
		));
		
	}
	
	//打开注册页面
	public function register_abstract(){
		//如果已登录，则直接跳转到管理系统内的提交摘要页面
		if(session('?user')){
			$user = session('user');
			$this->assign('user',$user);
			$this->assign('topic_list',C('TOPIC_LIST'));
			$this->assign('form_list',C('FORM_LIST'));
			$this->display("User/submit_abstract");
		}else{
			//如果未登录，则进入访客提交摘要页面
			$this->assign("title_list",C('TITLE_LIST'));
			$this->assign("country_list",C('COUNTRY_LIST'));
			$this->assign("topic_list",C('TOPIC_LIST'));
			$this->assign("form_list",C('FORM_LIST'));
	    	$this->display("User/register_abstract");
		}
		
		
    }
	
	//访客提交摘要，先用户注册,再提交摘要
	public function visitor_abstract(){
		$title_list = C('TITLE_LIST');
		$country_list = C('COUNTRY_LIST');
		// 获取表单的POST数据
		$data['title'] = $title_list[$_POST['title']];
		$data['first_name'] = trim($_POST['first_name']);
		$data['last_name'] = trim($_POST['last_name']);
		$data['affiliation'] = $_POST['affiliation'];
		$data['country'] = $country_list[$_POST['country']];
		$data['city'] = $_POST['city'];
		$data['address'] = $_POST['address'];
		$data['zip'] = $_POST['zip'];
		$data['email'] = $_POST['email'];
		$password = $_POST['password'];
		$salt = $this->random_str(6);
		$data['password'] = md5(md5($password) . $salt);
		$data['salt'] = $salt;
		
		//默认值
		//$data['updated_time'] = date("Y-m-d H:i:s");
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['active'] = 1;
		// 实例化User模型
		$User = M('User');
		//启动事务
		$User->startTrans();
		//先注册新用户
		$user_id = $User->add($data);
		if($user_id > 0){
			//根据id获取用户信息，并存入session，跳转到dashboard
			$user = $User->where("user_id = $user_id")->find();
			session('user',$user);
		}else{
			$User->rollback();
			$this->display('Public:500');
		}
		unset($data);
		$data['full_title'] = trim($_POST['full_title']);
		$data['topic'] = $_POST['topic'];
		$data['form'] = $_POST['form'];
		//默认状态为1，待审核
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['user_id'] = $user['user_id'];
		
		//注册新用户后再上传文件
		$upload = new \Think\Upload();		// 实例化上传类    
		$upload->maxSize   =     2097152 ;	// 设置附件上传大小为2M    
		$upload->exts      =     array('doc', 'docx');	// 设置附件上传类型  
		$upload->rootPath  =      './Public/'; 	// 设置附件上传根目录  
		$upload->savePath  =      './Uploads/'; 	// 设置附件上传目录
		$upload->saveName  =      array('getFileName',array($data['topic'],$user['first_name'],$user['last_name'])); 	// 设置上传文件名
		$upload->autoSub = FALSE;		// 关闭子目录保存
		// 上传单个文件     
		$info   =   $upload->uploadOne($_FILES['abstract_file']);
		 
		if(!$info) {
			// 事务回滚
			$User->rollback();
			// 上传错误提示错误信息        
			$this->error($upload->getError());  
			
		}else{
			// 上传成功        
			$data['filepath'] = $info['savepath'].$info['savename'];
		}
		
		$Abstract = M('Abstract');
		
		//向abstract表中插入一条新数据，并返回主键
		$abstract_id = $Abstract->add($data);
		
		if(!$abstract_id){
			// 事务回滚
			$User->rollback();
			$this->display('Public:500');
		}
		
		//获取邮件地址
		$toAddress = $user['email'];
		if(empty($toAddress)){
			$User->rollback();
			$this->display('Public:500');
		}
		//邮件主题
		$subject = "Submission Initiated";
		$title = $_POST['title'];
		//邮件正文
		$htmlBody = "Your submission for 6th International Conference on Biorefinery has been initiated.<br><br>".
			"This message serves as confirmation that your submission was received as noted below:<br>".
			"Title:	$title.<br><br>".
			"Thank you!";
		
		//发送电子邮件
		$response = sendMail($toAddress,$subject,$htmlBody);
		
		//如果操作成功
		if(isset($response) && $abstract_id > 0){
			//提交事务
			$User->commit(); 
			$this->success('Submit abstract successfully!', U('User/dashboard'),2);
		}else{
			$User->rollback();
			//删除上传文件
			$file = $data['filepath'];
			unlink($file);
			$this->display('Public:500');
		}
		
	}

	//打开登录页面
	public function login(){
		$this->display();
	}
	//用户登录操作
	public function user_login(){
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		if(empty($email) || empty($password)){
			$this->display('Public:500');
		}
		
		$User = M('User');
		$result = $User->where("email = '".$email."' and password = md5(concat(md5('".$password."'),salt))")->find();
		if($result){
			session('user',$result);
			$isAvailable = true;
		}else{
			$isAvailable = false;
		}
		
		echo json_encode(array(
		    'valid' => $isAvailable,
		));
		
	}
	//打开后台管理主页面
	public function dashboard(){
		$user = session('user');
		$user_id = $user['user_id'];
		
		//查询该用户提交的摘要
		$Abstract = M('Abstract');
		
		$abstracts = $Abstract->order("created_time")->where("user_id = $user_id")->select();
		
		$this->assign('abstracts',$abstracts);
		$this->assign('user',$user);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('form_list',C('FORM_LIST'));
		$this->display();
	}
	//打开空白页
	public function blank(){
		$this->display();
	}
	//打开提交摘要页面
	public function submit_abstract(){
		$user = session('user');
		$this->assign('user',$user);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('form_list',C('FORM_LIST'));
		$this->display();
	}
	//用户提交摘要操作
	public function user_abstract(){
		$user = session('user');
		$data['full_title'] = trim($_POST['full_title']);
		$data['topic'] = $_POST['topic'];
		$data['form'] = $_POST['form'];
		//默认状态为1，待审核
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['user_id'] = $user['user_id'];
		
		//先上传文件
		$upload = new \Think\Upload();		// 实例化上传类    
		$upload->maxSize   =     2097152 ;	// 设置附件上传大小为2M    
		$upload->exts      =     array('doc', 'docx');	// 设置附件上传类型  
		$upload->rootPath  =      './Public/'; 	// 设置附件上传根目录  
		$upload->savePath  =      './Uploads/'; 	// 设置附件上传目录
		$upload->saveName  =      array('getFileName',array($data['topic'],$user['first_name'],$user['last_name'])); 	// 设置上传文件名
		$upload->autoSub = FALSE;		// 关闭子目录保存
		// 上传单个文件     
		$info   =   $upload->uploadOne($_FILES['abstract_file']);
		 
		if(!$info) {
			// 上传错误提示错误信息        
			$this->error($upload->getError());  
		}else{
			// 上传成功        
			$data['filepath'] = $info['savepath'].$info['savename'];
		}
		
		$Abstract = M('Abstract');
		//启动事务
		$Abstract->startTrans();
		//事务处理
		//向abstract表中插入一条新数据，并返回主键
		$abstract_id = $Abstract->add($data);
		if(!$abstract_id){
			// 事务回滚
			$Abstract->rollback();
			$this->display('Public:500');
		}
		
		//获取邮件地址
		$toAddress = $user['email'];
		if(empty($toAddress)){
			$Abstract->rollback();
			$this->display('Public:500');
		}
		//邮件主题
		$subject = "Submission Initiated";
		$title = $_POST['title'];
		//邮件正文
		$htmlBody = "Your submission for 6th International Conference on Biorefinery has been initiated.<br><br>".
			"This message serves as confirmation that your submission was received as noted below:<br>".
			"Title:	$title.<br><br>".
			"Thank you!";
		
		//发送电子邮件
		$response = sendMail($toAddress,$subject,$htmlBody);
		
		//如果操作成功
		if(isset($response) && $abstract_id > 0){
			//提交事务
			$Abstract->commit(); 
			$this->success('Submit abstract successfully!', 'dashboard',2);
		}else{
			$Abstract->rollback();
			//删除上传文件
			$file = $data['filepath'];
			unlink($file);
			$this->display('Public:500');
		}
		
		
	}
	//用户注销
	public function logout(){
		//清除session
		session('user',null);
		$this->success('Logout successfully！', U('Index/index'),2);
	}
	
	
	//生成随机数,用于生成salt
    public function random_str($length){
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
    
    
	
}
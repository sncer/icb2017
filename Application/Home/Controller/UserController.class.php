<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
            case 'login':break;
			case 'select':break;
			case 'select_identity':break;
			case 'user_login':break;
            case 'register':break;
            case 'user_register':break;
            case 'verify_email':break;
			case 'forget_pwd':break;
			case 'get_new_pwd':break;
            default:parent::_checkLogin();
        }
        
    }
	
	//打开选择是否是新用户的页面
	public function select(){
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
    	$this->display();
	}
	
	//判断身份时候是新用户
	public function select_identity(){
		$identity = $_POST['identity'];
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
		//如果是缴费注册流程
		if($action == "reg"){
			//如果是老用户，则先登录；如果是新用户，则先注册
			if($identity == 1){
				$this->display("User:login");
			}else{
				$this->assign("title_list",C('TITLE_LIST'));
				$this->assign("country_list",C('COUNTRY_LIST'));
				$this->display("User:register");
			}
			
		}
		
		
		
	}
	
	//打开注册页面
	public function register(){
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		$this->assign("title_list",C('TITLE_LIST'));
		$this->assign("country_list",C('COUNTRY_LIST'));
		
    	$this->display();
    }
	//用户注册操作
	public function user_register(){
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
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
		$salt = random_str(6);
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
			if($action == "reg"){
				$this->assign("country_list",C('COUNTRY_LIST'));
				
				$this->success('Register successfully!', __MODULE__."/Registration/start_registration?action=reg",1);
			}else{
				$this->success('Register successfully!', U('User/dashboard'),1);
			}

		}else{
			$this->display('Public:500');
			exit;
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
	
	//打开登录页面
	public function login(){
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
		//如果已登录，则直接跳转到管理系统内的dashboard页面
		if(session('?user')){
			$this->success('You have already logined!', U('User/dashboard'),1);
		}else{
			$this->display();
		}

	}
	
	//用户登录操作
	public function user_login(){
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		if(empty($email) || empty($password)){
			$this->display('Public:500');
			exit;
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
	
	//打开忘记密码页面
	public function forget_pwd(){
		$this->display();
	}
	//获取新密码，发送邮件
	public function get_new_pwd(){
		$email = $_POST['email'];
		//查询email的用户
		$User = M('User');
		$user = $User->where("email = '$email'")->find();
		//如果email存在，则生成新密码，然后更新用户信息，发送邮件
		if($user){
			//生成新密码
			$password = random_pwd(6);
			$salt = random_str(6);
			$user['password'] = md5(md5($password) . $salt);
			$user['salt'] = $salt;
			$user['updated_time'] = date("Y-m-d H:i:s");
			//更新用户信息
			$res = $User->save($user); 
			if($res){
				//发送地址
				$toAddress = $user['email'];  
				//邮件主题
				$subject = "New Password of ICB2017 Account";
				$title = $user['title'];
				$last_name = $user['last_name'];
				$this->password_mail($toAddress,$subject,$title,$last_name,$password);
				
				$isAvailable = "success";
				
			}else{
				//更新用户失败
				$isAvailable = "fail";
			}
			
		}else{
			//邮件地址不存在
			$isAvailable = "error";
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
		
		$abstracts = $Abstract->order("created_time")->where("user_id = $user_id and status > 0")->select();
		
		//查询该用户提交的摘要
		$Reg = M('Reg');
		
		$regs = $Reg->order("created_time")->where("user_id = $user_id and status > 0")->select();
		
		$refer_type_list = C('REFER_TYPE_LIST');
		
		$this->assign('regs',$regs);
		$this->assign('user',$user);
		$this->assign('refer_type_list',$refer_type_list);
		
		$this->assign('abstracts',$abstracts);
		$this->assign('user',$user);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		$this->display();
	}
	//打开空白页
	public function blank(){
		$this->display();
	}
	
	//用户注销
	public function logout(){
		//清除session
		session('user',null);
		$this->success('Logout successfully！', U('Index/index'),1);
	}
	
	
	//发送忘记密码的电子邮件
	public function password_mail($toAddress,$subject,$title,$last_name,$password){
		
		//邮件正文
		$htmlBody = "Dear $title $last_name,<br><br>".
			"Your account of 6th International Conference on Biorefinery (ICB2017) has been changed. A new password has been generated randomly to replace the old one.<br><br>".
			"The details of the account are below:<br>".
			"Username:	$toAddress<br>".
			"Password:	$password<br><br>".
			"You can login the official website of ICB2017 with the Username and Password by following the link below:<br>".
			"<a href='http://icb2017.org/home/user/login.html' target='_blank'>http://icb2017.org/home/user/login.html</a><br><br>".
			"Please contact us if you have any questions.<br><br>".
			"ICB2017 Organizing Committee<br>".
			"Department of Chemical and Process Engineering<br>".
			"The University of Canterbury<br>".
			"Christchurch 8140, New Zealand<br>".
			"Email: ICB2017@canterbury.ac.nz<br>".
			"Website: <a href='http://www.icb2017.org' target='_blank'>www.icb2017.org</a><br>";
		
		//发送电子邮件
		$response = sendMail($toAddress,$subject,$htmlBody);
    }
	
}
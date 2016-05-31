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
	
	//打开登录页面
	public function login(){
		
		//如果已登录，则直接跳转到管理系统内的dashboard页面
		if(session('?user')){
			$this->success('You have already logined!', U('User/dashboard'),2);
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
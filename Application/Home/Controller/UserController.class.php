<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	public function register(){
		$this->assign("title_list",C('TITLE_LIST'));
		$this->assign("country_list",C('COUNTRY_LIST'));
		
    	$this->display();
    }
	
	public function user_register(){
		$title_list = C('TITLE_LIST');
		$country_list = C('COUNTRY_LIST');
		// 获取表单的POST数据
		$data['title'] = $title_list[$_POST['title']];
		$data['first_name'] = $_POST['first_name'];
		$data['last_name'] = $_POST['last_name'];
		$data['email'] = $_POST['email'];
		$data['address'] = $_POST['address'];
		$data['institute'] = $_POST['institute'];
		$data['department'] = $_POST['department'];
		$data['zip'] = $_POST['zip'];
		$data['city'] = $_POST['city'];
		$data['country'] = $country_list[$_POST['country']];
		$data['work_phone'] = $_POST['work_phone'];
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
			$this->success('Register successfully!', 'dashboard',2);
		}else{
			$this->display('Public:500');
		}
		
		
	}

	public function login(){
		$this->display();
	}

	public function dashboard(){
		$user = session('user');
		$this->assign('user',$user);
		$this->display();
	}

	public function blank(){
		$this->display();
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
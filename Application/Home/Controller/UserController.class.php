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
		// 获取表单的POST数据
		$data['title'] = $_POST['title'];
		$data['first_name'] = $_POST['first_name'];
		$data['last_name'] = $_POST['last_name'];
		$data['email'] = $_POST['email'];
		$data['address'] = $_POST['address'];
		$data['institute'] = $_POST['institute'];
		$data['department'] = $_POST['department'];
		$data['zip'] = $_POST['zip'];
		$data['city'] = $_POST['city'];
		$data['country'] = $_POST['country'];
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
		$result = $User->data($data)->add();
		dump($result);
	//	$this->display();
		
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
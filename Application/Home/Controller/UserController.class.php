<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
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
			exit;
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
			exit;
		}
		
		$User = M('User');
		$result = $User->where("email = '".$email."' and password = md5(concat(md5('".$password."'),salt))")->find();
		if($result){
			session('user',$result);
			$this->assign('user',$result);
			$this->display('User:dashboard');
		}else{
			$this->display('Public:500');
			exit;
		}
		
	}
	//打开后台管理主页面
	public function dashboard(){
		$user = session('user');
		$this->assign('user',$user);
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
		$this->display();
	}
	//提交摘要操作
	public function user_abstract(){
		$user = session('user');
		$data['title'] = $_POST['title'];
		$data['abstract'] = $_POST['abstract'];
		$data['keywords'] = $_POST['keywords'];
		//接受作者信息（数组）
		$first_name_arr = $_POST['first_name'];
		$last_name_arr = $_POST['last_name'];
		$email_arr = $_POST['email'];
		$degree_arr = $_POST['degree'];
		$affiliation_arr = $_POST['affiliation'];
		$is_corres = $_POST['is_corres'];
		
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
		$upload->saveName  =      array('uniqid',''); 	// 设置上传文件名
		// 上传单个文件     
		$info   =   $upload->uploadOne($_FILES['abstract_file']);
		 
		if(!$info) {
			// 上传错误提示错误信息        
			$this->error($upload->getError());  
			exit;  
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
			exit;
		}
		//清空数组
		unset($data);
		//作者数
		$num = count($first_name_arr);
		
		$Author = M('Author');
		
		//遍历所有作者
		for ($i=0; $i < $num; $i++) { 
			$data['abstract_id'] = $abstract_id;
			$data['first_name'] = $first_name_arr[$i];
			$data['last_name'] = $last_name_arr[$i];
			$data['email'] = $email_arr[$i];
			$data['degree'] = $degree_arr[$i];
			$data['affiliation'] = $affiliation_arr[$i];
			//如果是通讯作者，则is_corres为1
			if($is_corres[0] == $i){
				$data['is_corres'] = 1;
			}else{
				$data['is_corres'] = 0;
			}
			$data['created_time'] = date("Y-m-d H:i:s");
			$author_id = $Author->add($data);
			if(!$author_id){
				$Abstract->rollback();
				$this->display('Public:500');
				exit;
			}
		}
		
		//发送电子邮件
		sendMail();

		//如果操作成功
		if($author_id > 0 && $abstract_id > 0){
			//提交事务
			$Abstract->commit(); 
			$this->success('Submit abstract successfully!', 'dashboard',2);
		}
		
		
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
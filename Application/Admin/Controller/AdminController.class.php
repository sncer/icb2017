<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
			case 'admin_login':break;
			case 'login':break;
            default:parent::_checkLogin();
        }
        
    }
	
	public function admin_login(){
    	//如果已登录，则直接跳转到管理系统内的dashboard页面
		if(session('?admin')){
			$this->success('You have already logined!', U('Admin/dashboard'),1);
		}else{
			$this->display();
		}
    }
	public function login(){
		$admin_name = $_POST["admin_name"];
		$admin_pwd = $_POST["admin_pwd"];
		if($admin_name =="" || $admin_name == null || $admin_pwd == "" || $admin_pwd == null){
			$data = "Fail";
			$this->ajaxReturn($data,'json');
		}
		$Admin = M('Admin');
		$data = $Admin -> where("admin_name='$admin_name' AND admin_pwd=md5('$admin_pwd')")->find();
		if($data == null){
			$data = "Fail";
			$this->ajaxReturn($data,'json');
		}else{
			session('admin',$data);
			$data = "Success";
			$this->ajaxReturn($data,'json');
		}
		
	}
	//用户注销
	public function logout(){
		//清除session
		session('admin',null);
		$this->success('Logout successfully！', U('Admin/admin_login'),1);
	}
}
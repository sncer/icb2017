<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {
	public function admin_login(){
    	$this->display();
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
			session('admin_id',$data["admin_id"]);
			session('admin_name',$data["admin_name"]);
			$data = "Success";
			$this->ajaxReturn($data,'json');
		}
		
	}
}
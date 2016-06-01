<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	/* 初始化方法 */
	public function _initialize(){
        //判断用户是否已经登录
//      if (!isset($_SESSION['admin_id'])) {
//          $this->error('Please sign in first', U('Admin/admin_login'), 2);
//      }
	}
	/* 验证用户是否登录 */
	public function _checkLogin(){
		if (!isset($_SESSION['user'])) {
        	$this->error('Please sign in first', U('User/login'), 2);
        }
	}
	
	/* 获取用户请求的操作方法 */
  	final protected function getMethodName(){
		return ACTION_NAME;
    }
}

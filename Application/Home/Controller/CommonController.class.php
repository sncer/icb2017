<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
        //判断用户是否已经登录
        if (!isset($_SESSION['admin_id'])) {
            $this->error('Please sign in first', U('Admin/admin_login'), 1);
        }
	}
}

<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
//			case 'register_abstract':break;
            default:parent::_checkLogin();
        }
        
    }
	
	
	public function manage_user(){
		
		$User = M('User');
		
		$users = $User->table('icb_user user')->where('user.active > 0')->order('user.created_time desc')->group('user.user_id')
		->field('user.user_id,user.title,user.first_name,user.last_name,user.email,user.affiliation,user.address,user.zip,user.city,user.country,count(DISTINCT abs.abstract_id) abs_num, count(DISTINCT reg.reg_id) reg_num')
		->join('left join icb_abstract abs on abs.user_id = user.user_id and abs.status > 0')
		->join('left join icb_reg reg on reg.user_id = user.user_id and reg.status > 0')
		->select();
		
		
		$this->assign('users',$users);

		$this->display();
	}
	


}

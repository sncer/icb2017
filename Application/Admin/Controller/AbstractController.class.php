<?php
namespace Admin\Controller;
use Think\Controller;
class AbstractController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
//			case 'register_abstract':break;
            default:parent::_checkLogin();
        }
        
    }
	
	
	public function manage_abstract(){
		
		$Abstract = M('Abstract');
		
		
		
		$abstracts = $Abstract->table('icb_user user, icb_abstract abs')->where('abs.user_id = user.user_id')->order('abs.created_time desc')
		->field('abs.*,user.title,user.first_name,user.last_name,user.email,user.affiliation,user.address,user.zip,user.city,user.country')
		->select();
		
		
		$this->assign('abstracts',$abstracts);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		$this->display();
	}
	


}

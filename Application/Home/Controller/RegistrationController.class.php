<?php
namespace Home\Controller;
use Think\Controller;
class RegistrationController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
			case 'start_registration':break;
			case 'thanks':break;
            default:parent::_checkLogin();
        }
        
    }
	
	
	//打开会议注册页面
	public function start_registration(){
		
    	$this->display();
    }
	
	
	//打开注册完成后的感谢页面
	public function thanks(){
		
		$this->display();
	}
	
	
	
	
}
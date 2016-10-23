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
		
		$users = $User->table('icb_user user')->where('user.active > 0')->order('user.created_time')->group('user.user_id')
		->field('user.user_id,user.title,user.first_name,user.last_name,user.email,user.affiliation,user.address,user.zip,user.city,user.country,user.created_time,count(DISTINCT abs.abstract_id) abs_num, count(DISTINCT reg.reg_id) reg_num')
		->join('left join icb_abstract abs on abs.user_id = user.user_id and abs.status > 0')
		->join('left join icb_reg reg on reg.user_id = user.user_id and reg.status > 0')
		->select();
		
		
		$this->assign('users',$users);

		$this->display();
	}
	//查询提交摘要后没有注册的用户
	public function abs_noreg_user(){
		
		$User = M('User');
		
		$users = $User->table('icb_abstract abs')->where('abs.status > 0 and abs.user_id not in (select user_id from icb_reg)')->order('abs.created_time')
		->field('user.user_id,user.title,user.first_name,user.last_name,user.email,abs.full_title,abs.topic,abs.type,abs.created_time,abs.status')
		->join('left join icb_user user on abs.user_id = user.user_id and abs.status > 0')
		->select();
		
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		
		$this->assign('users',$users);

		$this->display();
	}

	//查询提交摘要后没有注册的用户并发送邮件
	public function abs_noreg_user_email(){
		
		$User = M('User');
		
		$users = $User->table('icb_abstract abs')->where('abs.status = 1 and abs.user_id not in (select user_id from icb_reg)')->order('abs.created_time')
		->field('user.user_id,user.title,user.first_name,user.last_name,user.email,abs.full_title,abs.topic,abs.type,abs.created_time')
		->join('left join icb_user user on abs.user_id = user.user_id and abs.status > 0')
		->select();
		
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		
		$this->assign('users',$users);

		$this->display();

	}

/*	
	//发送电子邮件
	public function send_mail($toAddress,$subject,$title,$last_name,$type){
		
		//邮件正文
		$htmlBody = "Dear $title $last_name<br><br>".
			"Your abstract submitted to the 6th International Conference on Biorefinery (ICB2017) has been received and assessed by the conference Scientific Committee. I am pleased to let you know that your paper has been accepted as Oral or Poster presentation and the abstract will be included in hard copy of conference proceedings.<br><br>".
			"You are now invited to take the benefits of early bird registration through the conference website (http://icb2017.org/home/index/registration.html). Please note that there is a luxury hotel and an economic motel you can choose for accommodation. Early registration will allow you to choose the preferred one.<br><br>".
			"Kind regards and look forward to welcoming you to ICB2017 in Christchurch, New Zealand.<br><br>".
			"ICB2017 Organizing Committee<br>".
			"The University of Canterbury<br>".
			"Christchurch 8140, New Zealand<br>".
			"Email: ICB2017@canterbury.ac.nz<br>".
			"Website: <a href='http://www.icb2017.org' target='_blank'>www.icb2017.org</a><br>";
		
		//发送电子邮件
		$response = sendMail($toAddress,$subject,$htmlBody);
    }
	
*/

}

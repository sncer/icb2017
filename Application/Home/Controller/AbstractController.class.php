<?php
namespace Home\Controller;
use Think\Controller;
class AbstractController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
			case 'register_abstract':break;
			case 'visitor_abstract':break;
            default:parent::_checkLogin();
        }
        
    }
	
	//打开提交摘要页面
	public function register_abstract(){
		//如果已登录，则直接跳转到管理系统内的提交摘要页面
		if(session('?user')){
			$user = session('user');
			$this->assign('user',$user);
			$this->assign('topic_list',C('TOPIC_LIST'));
			$this->assign('type_list',C('TYPE_LIST'));
			$this->display("Abstract:submit_abstract");
		}else{
			//如果未登录，则进入访客提交摘要页面
			$this->assign("title_list",C('TITLE_LIST'));
			$this->assign("country_list",C('COUNTRY_LIST'));
			$this->assign("topic_list",C('TOPIC_LIST'));
			$this->assign("type_list",C('TYPE_LIST'));
	    	$this->display("Abstract:register_abstract");
		}
    }
	
	//访客提交摘要，先用户注册,再提交摘要
	public function visitor_abstract(){
		$title_list = C('TITLE_LIST');
		$country_list = C('COUNTRY_LIST');
		// 获取表单的POST数据
		$data['title'] = $title_list[$_POST['title']];
		$data['first_name'] = trim($_POST['first_name']);
		$data['last_name'] = trim($_POST['last_name']);
		$data['affiliation'] = $_POST['affiliation'];
		$data['country'] = $country_list[$_POST['country']];
		$data['city'] = $_POST['city'];
		$data['address'] = $_POST['address'];
		$data['zip'] = $_POST['zip'];
		$data['email'] = $_POST['email'];
		$password = $_POST['password'];
		$salt = random_str(6);
		$data['password'] = md5(md5($password) . $salt);
		$data['salt'] = $salt;
		
		//默认值
		//$data['updated_time'] = date("Y-m-d H:i:s");
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['active'] = 1;
		// 实例化User模型
		$User = M('User');
		//启动事务
		$User->startTrans();
		//先注册新用户
		$user_id = $User->add($data);
		if($user_id > 0){
			//根据id获取用户信息，并存入session，跳转到dashboard
			$user = $User->where("user_id = $user_id")->find();
			session('user',$user);
		}else{
			$User->rollback();
			$this->display('Public:500');
			exit;
		}
		unset($data);
		$data['full_title'] = trim($_POST['full_title']);
		$data['topic'] = $_POST['topic'];
		$data['type'] = $_POST['type'];
		//默认状态为1，待审核
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['user_id'] = $user['user_id'];
		
		//注册新用户后再上传文件
		$upload = new \Think\Upload();		// 实例化上传类    
		$upload->maxSize   =     2097152 ;	// 设置附件上传大小为2M    
		$upload->exts      =     array('doc', 'docx');	// 设置附件上传类型  
		$upload->rootPath  =      './Public/Uploads'; 	// 设置附件上传根目录  
		$upload->savePath  =      '/Abstracts/'; 	// 设置附件上传目录
		$upload->saveName  =      array('getFileName',array($data['type'],$data['topic'],$user['first_name'],$user['last_name'])); 	// 设置上传文件名
		$upload->autoSub = FALSE;		// 关闭子目录保存
		
		//判断目标目录是否存在，不存在则新建
		$dir = './Public/Uploads/Abstracts';
		if(!is_dir($dir)){
			mkdir($dir); //新建目录 
		}
		// 上传单个文件     
		$info   =   $upload->uploadOne($_FILES['abstract_file']);
		 
		if(!$info) {
			// 事务回滚
			$User->rollback();
			// 上传错误提示错误信息        
			$this->error($upload->getError());  
			
		}else{
			// 上传成功        
			$data['filepath'] = $info['savepath'].$info['savename'];
		}
		
		$Abstract = M('Abstract');
		
		//向abstract表中插入一条新数据，并返回主键
		$abstract_id = $Abstract->add($data);
		
		if(!$abstract_id){
			// 事务回滚
			$User->rollback();
			//删除上传文件
			$file = "./Public/Uploads".$data['filepath'];
			if(file_exists($file)){
				unlink($file);
			}

			$this->display('Public:500');
			exit;
		}else{
			//提交事务
			$User->commit(); 
			$toAddress = $user['email'];
			//邮件主题
			$subject = "Submission Initiated";
			$title = $user['title'];
			$last_name = $user['last_name'];
			$full_title = trim($_POST['full_title']);
			$this->send_mail($toAddress,$subject,$title,$last_name,$full_title);
			$this->success('Submit abstract successfully!', U('User/dashboard'),2);
		}
	}

	//打开提交摘要页面
	public function submit_abstract(){
		$user = session('user');
		$this->assign('user',$user);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		$this->display();
	}
	//用户提交摘要操作
	public function user_abstract(){
		$user = session('user');
		$data['full_title'] = trim($_POST['full_title']);
		$data['topic'] = $_POST['topic'];
		$data['type'] = $_POST['type'];
		//默认状态为1，待审核
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		$data['user_id'] = $user['user_id'];
		
		//先上传文件
		$upload = new \Think\Upload();		// 实例化上传类    
		$upload->maxSize   =     2097152 ;	// 设置附件上传大小为2M    
		$upload->exts      =     array('doc', 'docx');	// 设置附件上传类型  
		$upload->rootPath  =      './Public/Uploads'; 	// 设置附件上传根目录  
		$upload->savePath  =      '/Abstracts/'; 	// 设置附件上传目录
		$upload->saveName  =      array('getFileName',array($data['type'],$data['topic'],$user['first_name'],$user['last_name'])); 	// 设置上传文件名
		$upload->autoSub = FALSE;		// 关闭子目录保存
		
		//判断目标目录是否存在，不存在则新建
		$dir = './Public/Uploads/Abstracts';
		if(!is_dir($dir)){
			mkdir($dir); //新建目录 
		}
		// 上传单个文件     
		$info   =   $upload->uploadOne($_FILES['abstract_file']);
		 
		if(!$info) {
			// 上传错误提示错误信息        
			$this->error($upload->getError());  
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
			//删除上传文件
			$file = "./Public/Uploads".$data['filepath'];
			if(file_exists($file)){
				unlink($file);
			}
			$this->display('Public:500');
			exit;
		}else{
			//提交事务
			$Abstract->commit();
			//获取邮件地址
			$toAddress = $user['email'];
			//邮件主题
			$subject = "Submission Initiated";
			$title = $user['title'];
			$last_name = $user['last_name'];
			$full_title = trim($_POST['full_title']);
			$this->send_mail($toAddress,$subject,$title,$last_name,$full_title);
			$this->success('Submit abstract successfully!', U('User/dashboard'),2);
			
		}
		
	}

	//摘要管理页面
	public function manage_abstract(){
		$user = session('user');
		$user_id = $user['user_id'];
		
		//查询该用户提交的摘要
		$Abstract = M('Abstract');
		
		$abstracts = $Abstract->order("created_time")->where("user_id = $user_id and status > 0")->select();
		
		$this->assign('abstracts',$abstracts);
		$this->assign('user',$user);
		$this->assign('topic_list',C('TOPIC_LIST'));
		$this->assign('type_list',C('TYPE_LIST'));
		$this->display();
	}
	
	//删除摘要
	public function delete_abstract(){
		$abstract_id = $_POST['abstract_id'];
		if(!isset($abstract_id)){
			$this->display('Public:500');
			exit;
		}
		$user = session('user');
		$user_id = $user['user_id'];
		//查询该用户提交的摘要
		$Abstract = M('Abstract');
		//执行查询操作
		$data = $Abstract->where("abstract_id = $abstract_id and user_id = $user_id")->find();
		if(empty($data)){
			$this->display('Public:500');
			exit;
		}
		
		//删除文件成功
		$result = $Abstract->where("abstract_id = $abstract_id and user_id = $user_id")->delete();
		if(empty($result)){
			//删除数据失败
			echo json_encode(array(
		    	'result' => "Fail!",
			));
		}
		
		$file = "./Public/Uploads".$data['filepath'];
		if(file_exists($file)){
			//文件存在即删除
			if(unlink($file)){
				//删除成功
				echo json_encode(array(
		    		'result' => "success",
				));
			}
		}else{
			echo json_encode(array(
		    	'result' => "File doesn't exist!",
			));
		}
	}
	
	//发送电子邮件
	public function send_mail($toAddress,$subject,$title,$last_name,$full_title){
		
		//邮件正文
		$htmlBody = "Dear $title $last_name<br><br>".
			"Your abstract submission for 6th International Conference on Biorefinery has been initiated.<br><br>".
			"This message serves as confirmation that your submission was received as noted below:<br>".
			"Submission Title:	$full_title.<br><br>".
			"Please contact us if you have any questions.<br><br>".
			"ICB2017 Organizing Committee<br>".
			"Department of Chemical and Process Engineering<br>".
			"The University of Canterbury<br>".
			"Christchurch 8140, New Zealand<br>".
			"Email: ICB2017@canterbury.ac.nz<br>".
			"Website: <a href='http://www.icb2017.org' target='_blank'>www.icb2017.org</a><br>";
		
		//发送电子邮件
		$response = sendMail($toAddress,$subject,$htmlBody);
    }
	
	
	

	
	
	
	
}
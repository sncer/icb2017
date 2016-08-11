<?php
namespace Home\Controller;
use Think\Controller;
class RegistrationController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
			case 'attendee_reg':break;
			case 'verify_email':break;
			case 'attendee_register':break;
			case 'start_registration':break;
			case 'thanks':break;
			case 'new_reg':break;
            default:parent::_checkLogin();
        }
        
    }
	
	//打开会议注册的注册页面，已经登录的用户可以跳过
	public function attendee_reg(){
		
			$this->assign("title_list",C('TITLE_LIST'));
			$this->assign("country_list",C('COUNTRY_LIST'));
			
	    	$this->display('attendee_reg');
		
    }
	
	//打开会议注册页面
	public function start_registration(){
		
		if(session('?user')){
			$this->assign("country_list",C('COUNTRY_LIST'));
			$this->assign("user",session('user'));
			$this->display('start_registration');
		}else{
			$this->assign("title_list",C('TITLE_LIST'));
			$this->assign("country_list",C('COUNTRY_LIST'));
			
	    	$this->display('attendee_reg');
		}
		
    }
	
	//与会人员注册，已注册可以跳过
	public function attendee_register(){
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
		$password = $this->random_pwd(6);
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
			//储存下随机密码
			//session('pwd',$password);
//			$this->assign('user',$result);
			//发送地址
			$toAddress = $data['email'];  
			//邮件主题
			$subject = "ICB2017 Account Created Automatically";
			$title = $data['title'];
			$last_name = $data['last_name'];
			$this->account_mail($toAddress,$subject,$title,$last_name,$password);
			//设置成功后跳转后台主页面的地址 
			$this->assign("country_list",C('COUNTRY_LIST'));
			$this->assign("user",session('user'));
			$this->display('start_registration');
		}else{
			$this->display('Public:500');
		}
	}
	//添加会议注册纪录
	public function add_registration(){
		$country_list = C('COUNTRY_LIST');
		//如果没有登录则报错
		if(!session('?user')){
			$this->display('Public:500');
		}
		//获取user_id
		$user_id = session("user")['user_id'];
		
		$data['user_id'] =  $user_id;
		//会议编号
		$data['refer_no'] = $this->gen_refer_no();
		//与会人员身份类型
		$data['refer_type'] = $_POST['refer_types'];
		//缴费方式，默认为1（信用卡）
		$data['pay_type'] = 1;
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		//是否需要邀请涵
		$data['is_visa'] = $_POST['is_visa'];
		
		$Reg = M('Reg');
		$Reg->startTrans(); 
		$reg_id = $Reg->add($data);
		
		//如果需要邀请函
		if($data['is_visa'] == 1){
			$visa['user_id'] =  $user_id;
			$visa['full_name'] = trim($_POST['full_name']);
			$visa['address'] = $_POST['address'];
			$visa['zip'] = $_POST['zip'];
			$visa['city'] = $_POST['city'];
			$visa['country'] = $country_list[$_POST['country']];
			$visa['pass_no'] = $_POST['pass_no'];
			$visa['pass_place'] = $_POST['pass_place'];
			$visa['pass_date'] = $_POST['pass_date'];
			$visa['expiry_date'] = $_POST['expiry_date'];
			$visa['birth_date'] = $_POST['birth_date'];
			$visa['birth_place'] = $_POST['birth_place'];
			$visa['birth_country'] = $country_list[$_POST['birth_country']];
			$visa['created_time'] = date("Y-m-d H:i:s");
			$Visa = M('Visa');
			$visa_id = $Visa->add($visa);
			if(!$visa_id){
				$Reg->rollback();
				$this->display('Public:500');
			}
			
		}
		if($reg_id){
			$Reg->commit();
			$curr_time = time();
			$early_time = strtotime (date("2016-11-01")); //Early Bird 截止期日
			$refer_type_list = C('REFER_TYPE_LIST');
			$payment_url_list = C('PAYMENT_URL_LIST');
			
			//如果在截止日期之前
			if($curr_time < $early_time){
				switch ($data['refer_type'])
				{
					case 1:
						$payment_url = $payment_url_list["1"];
						break;
					case 2:
						$payment_url = $payment_url_list["3"];
						break;
					case 3:
						$payment_url = $payment_url_list["5"];
						break;
					case 4:
						$payment_url = $payment_url_list["7"];
						break;
					default:
						$payment_url = "";
				}
			}else{
				switch ($data['refer_type'])
				{
					case 1:
						$payment_url = $payment_url_list["2"];
						break;
					case 2:
						$payment_url = $payment_url_list["4"];
						break;
					case 3:
						$payment_url = $payment_url_list["6"];
						break;
					case 4:
						$payment_url = $payment_url_list["8"];
						break;
					default:
						$payment_url = "";
				}
			}
			$this->assign("payment_url",$payment_url);
			$this->assign("refer_type_list",$refer_type_list);
			$this->assign("refer_type",$data['refer_type']);
			$this->assign("refer_no",$data['refer_no']);
			$this->assign("user",session("user"));
			$this->assign("visa",$visa);
			$this->display('thanks');
		}else{
			$Reg->rollback();
			$this->display('Public:500');
		}

	}
	
	//验证邮箱是否已经存在
	public function verify_email(){
		$email = $_POST['email'];
		// 实例化User模型
		$User = M('User');
		// Check email existence
		$res = $User->where("email = '".$email."'")->find();
		
		if($res){
			$isAvailable = false;
		}else{
			$isAvailable = true;
		}
		// Finally, return a JSON
		echo json_encode(array(
		    'valid' => $isAvailable,
		));
		
	}
	
	
	
	
	//打开注册完成后的感谢页面
	public function thanks(){
		
		$this->display();
	}
	
	public function new_reg(){
		//清除session
		session('user',null);
		$this->success('Please Wait a Moment...', U('Registration/attendee_reg'),0);
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
	//生成随机数,用于生成密码
    public function random_pwd($length){
        //生成一个包含数字的数组
        $arr = array_merge(range(0, 9));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++){
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
        return $str;
    }
	
	//生成编号，格式：ICB201700001
	public function gen_refer_no(){
		$Reg = M('Reg');
		$maxRegId = $Reg->max('reg_id');
		if(!$maxRegId){
			$maxRegId = 0;
		}
		$refer_no = "ICB2017".str_pad($maxRegId + 1,5,"0",STR_PAD_LEFT);
		return $refer_no;
		
	}
	
	//发送电子邮件
	public function account_mail($toAddress,$subject,$title,$last_name,$password){
		
		//邮件正文
		$htmlBody = "Dear $title $last_name,<br><br>".
			"Your account of 6th International Conference on Biorefinery (ICB2017) has been created automatically.<br><br>".
			"The details of the account are below:<br>".
			"Username:	$toAddress<br>".
			"Password:	$password<br><br>".
			"You can login the official website of ICB2017 with the Username and Password by following the link below:<br>".
			"<a href='http://icb2017.org/home/user/login.html' target='_blank'>http://icb2017.org/home/user/login.html</a><br><br>".
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
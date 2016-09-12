<?php
namespace Home\Controller;
use Think\Controller;
class RegistrationController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
//			case 'add_registration':break;

            default:parent::_checkLogin();
        }
        
    }
	
	//代别人注册页面，填写个人信息，写入reg表
	public function attendee_reg(){
		//获取操作流程，默认为reg_other
//		$action = $_REQUEST['action'];
//		$this->assign("action",$action);
		
		$this->assign("title_list",C('TITLE_LIST'));
		$this->assign("country_list",C('COUNTRY_LIST'));
		
    	$this->display('attendee_reg');
		
    }

	//添加与会人员保存在session中
	public function attendee_register(){
		//获取操作流程，默认为reg_other
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
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
		
		//将个人信息保存在session中
		session("attendee",$data);
		
		//打开注册缴费页面
		$this->assign("country_list",C('COUNTRY_LIST'));
		$this->display('start_registration');
	}

	//打开会议注册页面
	public function start_registration(){
		//如果没有登录则报错
		if(!session('?user')){
			$this->display('Public:500');
			exit;
		}
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		$this->assign("country_list",C('COUNTRY_LIST'));
		$this->display('start_registration');
		
    }
	
	//添加会议注册纪录
	public function add_registration(){
		//如果没有登录则报错
		if(!session('?user')){
			$this->display('Public:500');
			exit;
		}
		//获取操作流程
		$action = $_REQUEST['action'];
		$this->assign("action",$action);
		
		if(empty($action)){
			$this->display('Public:500');
			exit;
		}
		//如果是为自己注册
		if($action == "reg"){
			$user = session("user");
		}else{
			//如果是为其他人注册
			$user = session("attendee");
		}
		
		//获取国家列表
		$country_list = C('COUNTRY_LIST');
		//从session中获取当前操作者的user信息
		$user_id = session("user")['user_id'];
		
		
		$data['user_id'] =  $user_id;
		//会议编号
		$data['refer_no'] = gen_refer_no();
		//与会人员身份类型
		$data['refer_type'] = $_POST['refer_types'];
		//缴费方式，信用卡为1,汇款为2
		$data['pay_type'] = $_POST['pay_types'];
		
		//是否需要邀请涵
		$data['is_visa'] = $_POST['is_visa'];
		
		//从session中获取user信息
		$data['title'] = $user['title'];
		$data['first_name'] = $user['first_name'];
		$data['last_name'] = $user['last_name'];
		$data['email'] = $user['email'];
		$data['affiliation'] = $user['affiliation'];
		$data['address'] = $user['address'];
		$data['zip'] = $user['zip'];
		$data['city'] = $user['city'];
		$data['country'] = $user['country'];
		
		
		//订单状态，默认为1
		$data['status'] = 1;
		$data['created_time'] = date("Y-m-d H:i:s");
		
		$Reg = M('Reg');
		// 手动进行令牌验证，防止表单重复提交
		if (!$Reg->autoCheckToken($_POST)){
			// 令牌验证错误
			$this->assign("token",'false');
			$this->display('Registration:thanks');
			exit;
		}
		$Reg->startTrans(); 
		$reg_id = $Reg->add($data);
		
		if($reg_id){
			//如果需要邀请函
			if($data['is_visa'] == 1){
				$visa['reg_id'] =  $reg_id;
				$visa['user_id'] =  $user_id;
				$visa['full_name'] = trim($_POST['full_name']);
				$visa['address'] = $_POST['address'];
				$visa['zip'] = $_POST['zip'];
				$visa['city'] = $_POST['city'];
				$visa['country'] = $country_list[$_POST['country']];
				$visa['birth_date'] = $_POST['birth_date'];
				$visa['created_time'] = date("Y-m-d H:i:s");
				$Visa = M('Visa');
				$visa_id = $Visa->add($visa);
				if(!$visa_id){
					$Reg->rollback();
					$this->display('Public:500');
					exit;
				}
				
			}
			$Reg->commit();
			$curr_time = time();
			$early_time = strtotime (date("2016-11-01")); //Early Bird 截止期日
			$refer_type_list = C('REFER_TYPE_LIST');
			$payment_url_list = C('PAYMENT_URL_LIST');
			$total_cost_list = C('TOTAL_COST_LIST');
			
			//如果是信用卡支付
			if ($data['pay_type'] == 1) {
				//如果在截止日期之前
				if($curr_time < $early_time){
					//根据类型判断价格和支付链接
					switch ($data['refer_type'])
					{
						case 1:
							$payment_url = $payment_url_list["1"];
							$total_cost = $total_cost_list["1"];
							break;
						case 2:
							$payment_url = $payment_url_list["3"];
							$total_cost = $total_cost_list["3"];
							break;
						case 3:
							$payment_url = $payment_url_list["5"];
							$total_cost = $total_cost_list["5"];
							break;
						case 4:
							$payment_url = $payment_url_list["7"];
							$total_cost = $total_cost_list["7"];
							break;
						default:
							$payment_url = "";
							$total_cost = 0;
					}
					
				}else{
					switch ($data['refer_type'])
					{
						case 1:
							$payment_url = $payment_url_list["2"];
							$total_cost = $total_cost_list["2"];
							break;
						case 2:
							$payment_url = $payment_url_list["4"];
							$total_cost = $total_cost_list["4"];
							break;
						case 3:
							$payment_url = $payment_url_list["6"];
							$total_cost = $total_cost_list["6"];
							break;
						case 4:
							$payment_url = $payment_url_list["8"];
							$total_cost = $total_cost_list["8"];
							break;
						default:
							$payment_url = "";
							$total_cost = 0;
					}
				}
				$this->assign("payment_url",$payment_url);
				
			} else {
				//如果是转账
				//如果在截止日期之前
				if($curr_time < $early_time){
					//根据类型判断价格
					switch ($data['refer_type'])
					{
						case 1:
							$total_cost = $total_cost_list["1"];
							break;
						case 2:
							$total_cost = $total_cost_list["3"];
							break;
						case 3:
							$total_cost = $total_cost_list["5"];
							break;
						case 4:
							$total_cost = $total_cost_list["7"];
							break;
						default:
							$total_cost = 0;
					}
					
				}else{
					switch ($data['refer_type'])
					{
						case 1:
							$total_cost = $total_cost_list["2"];
							break;
						case 2:
							$total_cost = $total_cost_list["4"];
							break;
						case 3:
							$total_cost = $total_cost_list["6"];
							break;
						case 4:
							$total_cost = $total_cost_list["8"];
							break;
						default:
							$total_cost = 0;
					}
				}
				$this->assign("bank_account_details",C("BANK_ACCOUNT_DETAILS"));
			}
			
			$this->assign("pay_type",$data['pay_type']);
			$this->assign("total_cost",$total_cost);
			$this->assign("refer_type_list",$refer_type_list);
			$this->assign("refer_type",$data['refer_type']);
			$this->assign("refer_no",$data['refer_no']);
			$this->assign("reg",$data);
			$this->assign("visa",$visa);
			$this->display('thanks');
		}else{
			$Reg->rollback();
			$this->display('Public:500');
			exit;
		}

	}
	
	
	//打开注册完成后的感谢页面
	public function thanks(){
		
		$this->display();
	}
	
	
	
	//发送自动创建账户的电子邮件
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
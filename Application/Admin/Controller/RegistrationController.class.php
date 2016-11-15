<?php
namespace Admin\Controller;
use Think\Controller;
class RegistrationController extends CommonController {
	
	public function _initialize() {
		
		parent::_initialize();
		
		switch ($this->getMethodName()){
//			case 'register_abstract':break;
            default:parent::_checkLogin();
        }
        
    }
	
	//管理注册
	public function manage_reg(){
		
		//查询该用户提交的摘要
		$Reg = M('Reg');
		
		$regs = $Reg->order("created_time")->where("status = 1")->select();
		
		$refer_type_list = C('REFER_TYPE_LIST');
		
		$this->assign('regs',$regs);
		$this->assign('user',$user);
		$this->assign('refer_type_list',$refer_type_list);
		$this->display();

	}
	
	//注册详情
	public function details_reg(){
		$reg_id = $_REQUEST['reg_id'];
		if(!isset($reg_id)){
			$this->display('Public:500');
			exit;
		}
		
		$Reg = M('Reg');
		//查询该用户的注册单
		$data = $Reg->where("reg_id=$reg_id")->find();
		if(!$data){
			$this->display('Public:500');
			exit;
		}
		
		//如果需要邀请函,查询visa信息
		if($data['is_visa'] == 1){
			$Visa = M("Visa");
			$visa = $Visa->where("reg_id=$reg_id")->find();
			if(!$visa){
				$this->display('Public:500');
				exit;
			}
		}
		
		$curr_time = time();
		$early_time = strtotime (date("2016-11-16")); //Early Bird 截止期日
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
		
	}
	
	
	//查看需要邀请函的注册人员
	public function letter_reg(){
		//查询该用户提交的摘要
		$Reg = M('Reg');
		
		$regs = $Reg->table('icb_reg reg')->order("reg.created_time")->where("reg.status = 1 and reg.is_visa = 1")
		->field('reg.*,visa.full_name,visa.address as visa_address,visa.zip as visa_zip,visa.city as visa_city,visa.country as visa_country,visa.birth_date,count(DISTINCT abs.abstract_id) abs_num,abs.full_title,abs.type')
		->group('reg.reg_id')
		->join('left join icb_visa visa on reg.reg_id = visa.reg_id')
		->join('left join icb_abstract abs on reg.user_id = abs.user_id and concat(reg.first_name,reg.last_name) = (select concat(user.first_name,user.last_name) from icb_user user where user.user_id = reg.user_id )')
		->select();
		
		$refer_type_list = C('REFER_TYPE_LIST');
		
		$this->assign('regs',$regs);
		$this->assign('user',$user);
		$this->assign('refer_type_list',$refer_type_list);
		$this->assign('type_list',C('TYPE_LIST'));
		$this->display();
	}


}

<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
	public function _initialize() {
        parent::_initialize();
    }
    public function index(){
    	$href = "index";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function venue(){
		$href = "venue";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function chairman(){
		$href = "chairman";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function advisory(){
		$href = "advisory";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function scientific(){
		$href = "scientific";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function organizing(){
		$href = "organizing";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function abstracts(){
		$href = "abstracts";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function dates(){
		$href = "dates";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function program(){
		$href = "program";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function firstday(){
		$href = "firstday";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function registration(){
		$href = "registration";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function publications(){
		$href = "publications";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function contact(){
		$href = "contact";
    	$this->assign('href',$href);
    	$this->display();
	}
}
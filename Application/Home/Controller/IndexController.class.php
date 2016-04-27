<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
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
	public function committee(){
		$href = "committee";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function board(){
		$href = "board";
    	$this->assign('href',$href);
    	$this->display();
    }
	public function chairs(){
		$href = "chairs";
    	$this->assign('href',$href);
    	$this->display();
	}
	public function abstracts(){
		$href = "abstracts";
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
}
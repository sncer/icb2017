<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$this->display();
    }
	public function venue(){
    	$this->display();
    }
	public function committee(){
    	$this->display();
    }
}
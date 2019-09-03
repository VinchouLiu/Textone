<?php

namespace app\index\controller;
use think\Controller;
use think\Config;
use think\Request;

class Index extends Controller
{
    public function index()
    {
    	//当前为手机访问
    	if (Request::instance()->isMobile()){
    		$this->redirect('mobile/index/index',302);
    	} 
    	//当前为PC页面访问
        return $this->fetch();
    }

    public function tiaozhuan()
    {
    	
    }
}

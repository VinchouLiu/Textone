<?php
/**
 * 首页模块 欢迎页面
 */
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function tiaozhuan()
    {
    	$this->redirect('exam/examlist',302);
    }
}

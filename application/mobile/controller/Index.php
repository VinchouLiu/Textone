<?php

namespace app\mobile\controller;
use think\Controller;
use think\Config;

class Index extends Controller
{
    public function index()
    {
        $this->assign('barid',1);
        return $this->fetch();
    }

    public function tiaozhuan()
    {
    	$this->redirect('exam/examlist',302);
    }
}

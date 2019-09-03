<?php

namespace app\mobile\controller;
use think\Controller;
use think\Config;

class Project extends Controller
{
	public function index()
	{
        $this->assign('barid',3);
		return $this->fetch();
	}
}
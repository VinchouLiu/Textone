<?php

namespace app\mobile\controller;
use think\Controller;
use think\Config;
use app\common\controller\MobileController;

class News extends Controller
{
	public function index()
	{
		return $this->fetch();
	}

	/**
	 * 每日趣闻
	 * @method anecdote
	 * @return [type]   [description]
	 */
	public function anecdote()
	{
		$url = "https://news-at.zhihu.com/api/4/news/latest";
		$res = (new MobileController())->http_curl($url);
		// dump($res);
		$this->assign('list',$res);
		$url2 = "https://news-at.zhihu.com/api/3/news/hot";
		$res2 = (new MobileController())->http_curl($url);
		$this->assign('list2',$res2);
		dump($res2);
		return $this->fetch();
	}

	public function detail()
	{
		$newsid = input('get.newsid')
		$url = "https://news-at.zhihu.com/api/4/news/".$newsid;
		$res = (new MobileController())->http_curl($url);
		dump($res);
	}
}
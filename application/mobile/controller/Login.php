<?php
/**
 * Created by PhpStorm.
 * User: root1
 * Date: 2019-02-13
 * Time: 10:18
 */

namespace app\mobile\controller;


use app\common\controller\WxController;
use think\Config;
use think\Db;
use think\Session;
use weixin\Weixin;

class Login extends WxController
{
    /**
     * 获取微信用户数据页面
     * @return [type] [description]
     */
    public function getWx(){
        $appid = config("appid");
        $redirect_uri = urlencode ( config("Intranet").Config::get('redirect_uri') );
        $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header("Location:".$url);

    }

    public function getCode(){
        $code = $this->request->param('code');
        $userInfo = (new Weixin())->getUserInfo($code);
        Session::set('openid',$userInfo['openid']);
        // if(!empty($userInfo)){
        //     $this->redirect('login/index');
        // }else{
        //     $this->getWx();
        // }
        dump($userInfo);
        return $this->display('<a href="/">进入首页</a>');
    }
}
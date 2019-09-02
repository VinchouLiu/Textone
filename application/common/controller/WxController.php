<?php
namespace app\common\controller;
use app\index\controller\Login;
use app\index\model\user\Model_user;
use think\Controller;
use think\Session;
use weixin\Weixin;

class WxController extends Controller{

    protected $oUser;
    protected $wxUser;

    public function _initialize(){
        parent::_initialize();
        $this->oUser = Session::has('oUser') ? Session::get('oUser') : false;
        $this->wxUser = Session::has('wxUser') ? Session::get('wxUser') : false;

        // 本地pc端测试时解开注释, 数字9为用户id
        // $this->oUser  = (new Model_user())->getDataById(1);
        // $this->wxUser = (new Model_user())->getDataById(1);   
        // session('user_id',1);
        // $this->is_attention = 1;
    }

    /**
     * 验证用户信息是否正确
     */
    public function wxLogin(){
        if (!$this->oUser || !$this->wxUser){
            (new Login())->getWx();
        }
        $user = (new Model_user())->getDataById($this->oUser['id']);
        if(!$user){
            (new Login())->getWx();
        }
        $user = (new Model_user())->getDataByOpenId($this->wxUser['openid']);
        if(!$user){
            (new Login())->getWx();
        }
    }





}
?>
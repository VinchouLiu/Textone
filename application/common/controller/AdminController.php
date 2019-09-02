<?php
namespace app\common\controller;
use think\Controller;
use app\admin\model\sys\Model_log_act;
// use app\admin\model\sys\Model_menu;
// use app\admin\model\sys\Model_role;
use app\admin\model\Model_role;

class AdminController extends Controller{

	public function _initialize(){
		parent::_initialize();
		//验证登录
        $tmp = explode('?', $_SERVER['REQUEST_URI']);
        if ($tmp[0] != '/admin/login/')    
            $this->checkLogin();

        // $request = \think\Request::instance();
        // $aSys = $this->request->dispatch();
        // $this->className = $aSys['module'][0] . '/' . $aSys['module'][1];	
        // $this->funcName=$aSys['module'][2];
        //$this->checkRole();
	}

	/**
     * 验证登录
     *
     * @author wangpeng <117965722@qq.com>
     * @date   2018-09-03
     *
     * @return [type]     [description]
     */
    public function checkLogin(){
    	$iAdmin = session('admin');

    	if (!$iAdmin){
    		// $this->redirect('/admin/login/');
            print_r("<script>window.location='/admin/login/'</script>");die; 
        }
    }

       /**
     * 添加日志
     *
     * @author wangpeng <117965722@qq.com>
     * @date   2018-09-03
     *
     * @param  array      $array [添加条件]
     * 
     */
    public function Log($class_name,$func_name,$content){
        
        $aData = array(
            'class_name'   => $class_name,
            'func_name'    => $func_name,
            'content'      => $content,
            'admin_id'     => session('admin_id'),
            'admin_name'   => session('username'),
            'act_ip'       => func_get_ip(),
        );
        if($aData)
            $aResult = db('sys_log_act')->insert($aData);

        if ($aResult) return true;
        else return FALSE;
    }

    /// <summary>
    /// 获取用户的权限信息
    /// </summary>
    public function checkEditOrDelete($module_id,$RoleType='Add,Edit,Delete')
    {
        $role = explode(',',$RoleType);
        $TypeString='';
        
        foreach($role as $v)
        {
            if ($this->checkAdminLevel($module_id, $v))
                $TypeString.=$v.',';
        }
        return rtrim($TypeString,',');
    }

    /// <summary>
    /// 判断用户是否有此模块的权限
    /// </summary>
    public function checkAdminLevel($module_id,$ActionType)
    {
        if(session('admin'))
        {
            if(session('admin')['issystem']==0)
                return true;
            $list=$this->sessionRoleModule();
            foreach($list as $v)
            {
                if($v['actiontype']==$ActionType&&$v['module_id']==$module_id)
                {
                    return true;
                }
            }
        }
        return false;
    }

    /// <summary>
    /// 获取用户的权限信息
    /// </summary>
    public function sessionRoleModule()
    {
        
        if(session('role_module'))
        {
            $this->role=new Model_role();
            $list=$this->role->GetModulesByRoleID(session('admin')['role_id']);
            session('role_module', $list);
        }
        return session('role_module');
    }

    /**
     * 判断角色权限防止手动输入其他页面
     * @return [type] [description]
     */
    public function checkRole(){

        $role_id=session('role_id');
        if(!empty($role_id)){
            $oRole=new Model_role();
            $oMenu=new Model_menu();
            $data=$oRole->getData(['where'=>['role_id'=>$role_id]]);
            $datas=$oMenu->top(['where'=>['menu_id'=>['in',$data['intro']]]]);
            $class=$func=array();
            array_push($class,"admin/dashboard.dashboard");
            array_push($class,"admin/sys.login");
            foreach($datas as $v){
                array_push($class,$v['class_name']);
            }
            if(!in_array($this->className, $class)){
              throw new \think\exception\HttpException(404, '页面不存在');
            }
        }
    }

}

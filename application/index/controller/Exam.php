<?php

namespace app\index\controller;

use think\Controller;
use app\index\model\Model_exam;

class Exam extends Controller{

    public function _initialize()
    {
        parent::_initialize();
        $this->oExam = new Model_exam();
    }

    public function index()
    {
        echo "考试页面首页";
    }
	/**
	 * 考试列表页面
	 * @method examdelist
	 * @return [type]     [description]
	 */
	public function examlist()
	{
		return $this->fetch();
	}

    /**
     * 下拉流加载
     * @method ajaxlist
     * @return [type]   [description]
     */
    public function ajaxlist()
    {
        $Post = input('post.');
        $res = $this->oExam->getexamList($Post);
        return json($res);
    }
	/**
     * 考试详情页面
     * @method ExamDatail
     */
    public function examdetail()
    {
    	$res = db('question')->select();
        foreach ($res as $key => $value) {
            $res[$key]['question_option'] = (array)json_decode($value['question_option']);
        }
        $this->assign('data',$res);
        return $this->fetch();
    }

    /**
     * 考试结果页
     * @method ExamResult
     */
    public function examresult()
    {
    	$res = db('question')->select();
        $count = db('question')->count();
        // dump($res);
        if(request()->isAjax()){
            $input = input('post.');
            // dump($input['data']);
            if(empty($input['data'])){
                return ['status'=>'warn','msg'=>'考试未通过','content'=>'请再接再厉,你的得分为:0分,你共答对0道试题'];
            }
            // 提交的考试答案结果处理
            foreach ($input as $k => $val) 
            {
                foreach ($val as $k2 => $val2){
                        if(is_array($val2)){
                            foreach ($val2 as $key => $value){
                                $tt = substr($value,1,strlen($value)-2);
                                $answer[$tt][$key] = substr($value,-1);
                            }
                            $answer[$tt] = implode(',', $answer[$tt]);
                        }else{
                            $tt = substr($val2,1,strlen($val2)-2);
                            $answer[$tt] = substr($val2,-1);
                        }
                    }
            }
            //判断答题结果
            $sum_score = 0;//得分
            foreach ($res as $key => $value) {
                foreach ($answer as $k => $val) {
                    if($k==$value['id']){
                        if($val==$value['question_right_key'])
                        {
                            $sum_score += 10;
                        }   
                    }
                }
            }
            //判断是否通过考试
            $temp = round($sum_score/$count*10,2);//  得分/试卷分数 保留两位小数
            $true = $sum_score/10;
            if($temp>60){
                return ['status'=>'success','msg'=>'考试通过','content'=>'恭喜,你的得分为:'.$temp.'分,你共答对'.$true.'道试题'];
            }else{
                return ['status'=>'warn','msg'=>'考试未通过','content'=>'请再接再厉,你的得分为:'.$temp.'分,你共答对'.$true.'道试题'];
            }
        }
    }
}
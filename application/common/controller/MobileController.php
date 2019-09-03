<?php
namespace app\common\controller;
use think\Controller;

class MobileController extends Controller{

	/**
     * //功能：curl通讯
     * @method http_curl
     * @param  [type]    $url  [description]
     * @param  string    $type [description]
     * @param  string    $res  [description]
     * @param  string    $arr  [description]
     * @return [type]          [description]
     */
    public function http_curl($url,$type='get',$res='json',$arr=''){
        //1.初始化curl
        $ch =curl_init();
        //2.设置curl的参数
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        // 关闭SSL验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //3.采集
        $output =curl_exec($ch);
        //4.关闭
        if($res=='json'){
            if(curl_error($ch)){
                //请求失败，返回错误信息
                dump(curl_error($ch));
            }else{
                //请求成功，返回错误信息
                $res = json_decode($output,true);
                return $res;
            }
        }
        curl_close($ch);
    }

}
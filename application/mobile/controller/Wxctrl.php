<?php
/**
 * 微信操作类控制器
 */

namespace app\mobile\controller;

use think\Controller;
use think\Config;
use weixin\Weixin;
use app\common\controller\MobileController;

class Wxctrl extends Controller
{
	/**
 	 * 微信端口配置
 	 * @method checkSignature
 	 * @return [type]         [description]
 	 */
    public function checkSignature()
    {
    	$token = config("wxtoken");
    	// dump($token);
        //先获取到这三个参数
        $signature = input('get.signature');
        $nonce = input('get.nonce');
        $timestamp = input('get.timestamp');
		 //把这三个参数存到一个数组里面
        $tmpArr = array($timestamp,$nonce,$token);
        //进行字典排序
        sort($tmpArr);
        //把数组中的元素合并成字符串，impode()函数是用来将一个数组合并成字符串的
        $tmpStr = implode($tmpArr);
        //sha1加密，调用sha1函数
        $tmpStr = sha1($tmpStr);
        // dump($tmpStr);
        // dump($signature);
        //判断加密后的字符串是否和signature相等
        if($tmpStr == $signature)
        {
           $echostr = input('get.echostr');
	        if($echostr)
	        {
	            echo $echostr;
	            exit;
	        }
        }
        return true;
    }

	public function responseMsg() {
		//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postStr = file_get_contents('php://input');
		if (!empty($postStr)) {
			$postObj = simplexml_load_string($postStr); //, 'SimpleXMLElement', LIBXML_NOCDATA
			//关注事件时推送消息
			if (strtolower($postObj->MsgType) == 'event') {//事件
				if (strtolower($postObj->Event == 'subscribe')) {//关注
					$domain = Yii::$app->params['domain']['www'];
					$imgDomain = Yii::$app->params['domain']['img'] . 'logo.png';
					$array = array(
						array('title' => '欢迎关注一网超市',
							'description' => '上一网 逛超市 多快好省！',
							'picUrl' => $imgDomain,
							'url' => $domain,
						),
					);
					$textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>" . count($array) . "</ArticleCount>
                        <Articles>";
					foreach ($array as $key => $val) {
						$textTpl .= "<item>
            <Title><![CDATA[" . $val['title'] . "]]></Title> 
            <Description><![CDATA[" . $val['description'] . "]]></Description>
            <PicUrl><![CDATA[" . $val['picUrl'] . "]]></PicUrl>
            <Url><![CDATA[" . $val['url'] . "]]></Url>
            </item>";
					}
					$textTpl .= "
            </Articles>
            </xml>";
					$fromUserName = $postObj->FromUserName;
					$toUserName = $postObj->ToUserName;
					$time = time();
					echo sprintf($textTpl, $fromUserName, $toUserName, $time, 'news');
					$ret = array('openid' => "$fromUserName", 'event' => 'subscribe');
					return json_encode($ret);
				} else if ($postObj->Event == "VIEW") {
					$fromUserName = $postObj->FromUserName;
					$ret = array('openid' => "$fromUserName", 'event' => 'VIEW');
					return json_encode($ret);
				} else if ($postObj->Event == "CLICK") {
					$fromUserName = $postObj->FromUserName;
					$ret = array('openid' => "$fromUserName", 'event' => 'VIEW');
					return json_encode($ret);
				} else {

					$fromUserName = $postObj->FromUserName;
					$ret = array('openid' => "$fromUserName", 'event' => 'VIEW');
					return json_encode($ret);
				}
			} else {//$postObj->MsgType=text 在公众号发消息收到的就是text类型
				echo 'success';//这里就是回复的空字符串或者success,即echo 'success';
				exit;//这个退出是关键，必须加上，没有则还是会出现那个标题的提示
			}
		} else {
			echo 'success';
			exit;
		}
	}

	public function responseMsg2(){
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		//extract post data
		if (!empty($postStr)){
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$RX_TYPE = trim($postObj->MsgType);

		switch($RX_TYPE){
						//当回复公众号图片时
		case "image":
		$resultStr = $this->handleImage($postObj);
		break;
							//当回复公众号关键字时
		case "text":
		$resultStr = $this->handleText($postObj);
		break;
							//关注公众号事件
		case "event":
		$resultStr = $this->handleEvent($postObj);
		break;
		default:
		$resultStr = "Unknow msg type: ".$RX_TYPE;
		break;
		}
		echo $resultStr;
		}else {
		echo "";
		exit;
		}
	}


//回复图片消息
private function handleImage($object)
{
 $img_list = Db::name("article")->where("pass",'yes')->order('px asc')->limit(8)->select();

 $content = array();
 if(isset($img_list) && !empty($img_list)){
  foreach($img_list as $key=>$item){
  $content[] = array(
   "Title"=>$item['article_name'],
   "Description"=>$item['article_desc'],
   "PicUrl"=>"http://域名/".$item['article_img'],
   "Url" =>"http://域名/".Url::build('home/article/index',"id=".$item['id'])
   );
  }
 }

 $itemTpl = "<item>
  <Title><![CDATA[%s]]></Title>
  <Description><![CDATA[%s]]></Description>
  <PicUrl><![CDATA[%s]]></PicUrl>
  <Url><![CDATA[%s]]></Url>
 </item>";
 $item_str = "";
        //多图文回复
 foreach ($content as $item){
  $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
 }
 $xmlTpl = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[news]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>$item_str</Articles>
 </xml>";

 $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($content));
 return $result;
}
public function handleText($postObj)
{
 $fromUsername = $postObj->FromUserName;
 $toUsername = $postObj->ToUserName;
 $keyword = trim($postObj->Content);
 $time = time();
 $textTpl = "<xml>
   <ToUserName><![CDATA[%s]]></ToUserName>
   <FromUserName><![CDATA[%s]]></FromUserName>
   <CreateTime>%s</CreateTime>
   <MsgType><![CDATA[%s]]></MsgType>
   <Content><![CDATA[%s]]></Content>
   <FuncFlag>0</FuncFlag>
   </xml>";
 if(!empty( $keyword )){
  $msgType = "text";
  if($keyword == '1'){
  $contentStr = '关键字回复1';
  }elseif($keyword == '2'){
  $contentStr = '关键字回复2';
  }

  $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
  echo $resultStr;
 }
}
 public function handleEvent($object){
 $contentStr = "";
 $keyArray = explode("_", $object->EventKey);
 $EventKey = $object->EventKey;
 $FromUserName = $object->FromUserName;
 $key_count = count($keyArray);
 switch ($object->Event){
  //首次关注
  case "subscribe":
  $contentStr = "嘻嘻，欢迎关注优倍素材网";
  break;
  //已关注
  case "SCAN":
  $contentStr = "嗨，老朋友，欢迎回来";
  break;
  default :
 }

 $resultStr = $this->responseText($object, $contentStr);
 return $resultStr;
}
public function responseText($object, $content, $flag=0)
{
 $textTpl = "<xml>
   <ToUserName><![CDATA[%s]]></ToUserName>
   <FromUserName><![CDATA[%s]]></FromUserName>
   <CreateTime>%s</CreateTime>
   <MsgType><![CDATA[text]]></MsgType>
   <Content><![CDATA[%s]]></Content>
   <FuncFlag>%d</FuncFlag>
   </xml>";
 $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
 return $resultStr;
}
//微信验证
public function valid(){ 
 $echoStr = $_GET["echostr"];
 if($this->checkSignature()){
  echo $echoStr;
 exit;
 }
 }

 //检查微信签名
 private function checkSignature(){
 $signature = $_GET["signature"];
 $timestamp = $_GET["timestamp"];
 $nonce = $_GET["nonce"];    
 $token = TOKEN;
 $tmpArr = array($token, $timestamp, $nonce);
 sort($tmpArr);
 $tmpStr = implode( $tmpArr );
 $tmpStr = sha1( $tmpStr );
if( $tmpStr == $signature ){
return true;
}else{
return false;
}
}



	/**
	 * 创建自定义菜单 浏览器执行一次
	 * @method zidingyi
	 * @return [type]   [description]
	 */
	public function zidingyi()
	{
		$web = config("Intranet");//内网域名
		header('content-type:text/html;charset=utf-8');
		$weixin = new weixin();
		$access_token = $weixin->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		$postArr=array(
			'button'=>array(
			array(
			'name'=>urlencode('精选答题系统'), // 'type'=>'click',
            'type'=>'view', // 'key'=>'item1',
			'url'=>$web.'index/exam/examlist'),
			array(
			'name'=>urlencode('点外卖'),
			'type'=>'view',
			'url'=>'http://www.tcode.me',
			'sub_button'=>array(
			array(
			'name'=>urlencode('歌曲'),
			'type'=>'view',
			// 'key'=>'songs',
			'url'=>'http://www.tcode.me'
			),//第一个二级菜单
			array(
			'name'=>urlencode('电影'),
			'type'=>'view',
			'url'=>'http://www.tcode.me'
			),//第二个二级菜单
			)
		),
        array(
        'name'=>urlencode('视频游戏'),
        'type'=>'view',
        'url'=>'http://www.tcode.me',
                        ),//第三个一级菜单

        ));
	    echo $postJson = urldecode(json_encode($postArr));
	    $res = (new MobileController())->http_curl($url,'post','json',$postJson);
	    dump($res);
    }

    

    /**
     * 微信消息推送函数  模拟前端ajax传递信息 后端执行进入后 微信消息推送
     * @method xiaoxi
     * @return [type] [description]
     */
    public function pushMsg(){
    	$weixin = new weixin();
    	$openid = session('openid');
    	$qq = 111;
    	/*{{first.DATA}} 信息1:{{orderno.DATA}} 信息2:{{refundno.DATA}} 信息3:{{refundproduct.DATA}} 信息4:{{remark.DATA}}*/
    	$data=[
	            'touser'=>$openid,
	            'template_id'=>'7ZVpkQ3esgRLdJg-XdhkDbPt38VX47DygGFvODUm83A',
	            'url'=>$web_url = "http://".$_SERVER['SERVER_NAME'],
	            'topcolor'=>"#FF0000",
	            'data'=>array(
	                'first'=>array('value'=>"恭喜您报名成功",'color'=>"#fc0101"),
	                'orderno'=>array('value'=>'派送信息：姓名：'.$qq.';联系方式：'.$qq.';地址：'.$qq.';具体商品信息请到后台查询；','color'=>"#173177"),
	                'refundno'=>array('value'=>'17604840949','color'=>"#173177"),
	                'refundproduct'=>array('value'=>date("Y-m-d H:i:s",time()),'color'=>"#173177"),
	                'remark'=>array('value'=>"您的报名信息已提交，请耐心等待",'color'=>"#173177"),
	            )
                ];
        $access_token = $weixin->getAccessToken();
    	$json_data=json_encode($data);//转化成json数组让微信可以接收
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;//模板消息请求URL
        $res=(new MobileController())->http_curl($url,'post','json',urldecode($json_data));//请求开始

        if($res['errcode']==0 && $res['errcode']=="ok"){
        	//发送成功
            return ['code'=>1];
        }else{
        	//发送失败
            return ['code'=>-4];
        }
    }

}





?>

<?php
header("Content-Type: text/html;charset=utf-8");
/*--------------------------------
功能:PHP发送短信接口
修改日期:	2013-02-22
说明:		http://www.gysoft.cn/smspost/send.aspx?username=用户账号&password=&mobile=号码&content=内容
--------------------------------*/
$uid = $_POST["uid"];		//用户账号
$pwd = $_POST["pwd"];		//密码
$mobile	 = $_POST["mobile"];	//号码
$content = $_POST["content"];  //内容
//即时发送
$res = sendSMS($uid,$pwd,$mobile,$content);
echo json_encode($res);
function sendSMS($uid,$pwd,$mobile,$content)
{
	$http = 'http://www.gysoft.cn/smspost_utf8/send.aspx';
	$data = array
		(
		'username'=>$uid,			//用户账号
		'password'=>$pwd,	        //密码
		'mobile'=>$mobile,			//号码
		'content'=>$content      	//内容
		);
	$re= postSMS($http,$data);			//POST方式提交
	if( substr($re,0,2) == 'OK' )  //返回结果为OK1表示成功1条，OK2 表示成功二条，以此类推
	{
		return array("result"=>true,"msg"=>"发送成功!");
	}
	else 
	{
		return array("result"=>false,"msg"=>"发送失败! 状态：".$re); //返回错误的详细提示
	}
}

function postSMS($url,$data='')
{
	$row = parse_url($url);
	$host = $row['host'];
	$port = isset($row['port']) ? $row['port']:80;
	$file = $row['path'];

	$post = '';
	while (list($k,$v) = each($data)) 
	{
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$post = substr( $post , 0 , -1 );
	$len = strlen($post);
	$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
	if (!$fp) {
		return "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $file HTTP/1.1\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n\r\n";
		$out .= $post;		

		fwrite($fp, $out);
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
		$receive = explode("\r\n\r\n",$receive);
		unset($receive[0]);
		return implode("",$receive);
	}
}
?>
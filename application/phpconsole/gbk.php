<?php 
//GBK页面

//include_once 'e4phpconsole.php';

//include_once 'C:\xampp\xampp174\xampp\htdocs\phpconsole\e4phpconsole.php';


define('DEFAULTREMARK', 'normal');
include_once 'i.php';

class a{
	public function test(){
		//调试所在位置的上下文信息
		bugthis();
		
		//无页面输出，控制台每个输出都换行
		//println(array("println",'23'=>"asdasd"));
	}
}
$_a = new a();
function aaa(){
$_a = new a();
$_a->test();
}
aaa('sda');
//array('as' => 'asdasd\r\n',0 => 'asdasd\r\n',1 => 'asdasd\r\n',2 => 'asdasd
//','class' => (a),32 => array(0 => 'asdasd',1 => 23.4234,2 => -34543),'bool' => ture,'null' => null,'numeric' => '4353')
println(array('as'=>'asdasd\\r\\n','asdasd\r\n',"asdasd\\r\\n","asdasd\r\n",'class'=>$_a,
		32=>array('asdasd',23.4234,-34543),'bool'=>true,'null'=>null,'numeric'=>'4353'));
    //页面输出不换行，控制台每个输出都换行
	echo_p(array("echo_p",'23'=>"asdasd"));
	
	//页面输出换行，控制台每个输出换行
    echobr_p(array("echobr_p",'23'=>"asdasd"));

    //数组页面输出不换行，控制台每个输出都换行
    echoarr(array("echoarr",'23'=>"asdasd"));
	
    //数组页面输出换行，控制台每个输出换行
    echobrarr(array("echobrarr",'23'=>"asdasd"));

    //数组无页面输出，控制台每个输出都换行
    printarr(array("printarr",'23'=>"asdasd"));

   

echobr_p(array("阿什顿jad",'23'=>"asdasd"));

//echo 'get_charset<br>';
//echo get_charset("asdadasdasd").'<br>'; //GBK
//echo get_charset("asdadasda暗示大家爱看的就看sd").'<br>';//GBK
//echo get_charset("数据将大幅扩大设计开发").'<br>';//GBK

//echo 'is_utf8<br>';
//echo is_utf8("asdadasdasd").'<br>';//UTF-8
//echo is_utf8("asdadasda暗示大家爱看的就看sd").'<br>';//not UTF-8
//echo is_utf8("数据将大幅扩大设计开发").'<br>';//not UTF-8
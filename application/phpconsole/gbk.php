<?php 
//GBKҳ��

//include_once 'e4phpconsole.php';

//include_once 'C:\xampp\xampp174\xampp\htdocs\phpconsole\e4phpconsole.php';


define('DEFAULTREMARK', 'normal');
include_once 'i.php';

class a{
	public function test(){
		//��������λ�õ���������Ϣ
		bugthis();
		
		//��ҳ�����������̨ÿ�����������
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
    //ҳ����������У�����̨ÿ�����������
	echo_p(array("echo_p",'23'=>"asdasd"));
	
	//ҳ��������У�����̨ÿ���������
    echobr_p(array("echobr_p",'23'=>"asdasd"));

    //����ҳ����������У�����̨ÿ�����������
    echoarr(array("echoarr",'23'=>"asdasd"));
	
    //����ҳ��������У�����̨ÿ���������
    echobrarr(array("echobrarr",'23'=>"asdasd"));

    //������ҳ�����������̨ÿ�����������
    printarr(array("printarr",'23'=>"asdasd"));

   

echobr_p(array("��ʲ��jad",'23'=>"asdasd"));

//echo 'get_charset<br>';
//echo get_charset("asdadasdasd").'<br>'; //GBK
//echo get_charset("asdadasda��ʾ��Ұ����ľͿ�sd").'<br>';//GBK
//echo get_charset("���ݽ����������ƿ���").'<br>';//GBK

//echo 'is_utf8<br>';
//echo is_utf8("asdadasdasd").'<br>';//UTF-8
//echo is_utf8("asdadasda��ʾ��Ұ����ľͿ�sd").'<br>';//not UTF-8
//echo is_utf8("���ݽ����������ƿ���").'<br>';//not UTF-8
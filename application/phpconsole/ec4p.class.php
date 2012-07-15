<?php
/**
 **************************************
 * EC4P
 * ����һ��JAVAת����PHP����Ա��ϰ����Windows�µ�eclipseʹ��
 * ���Ե�ʱ��ϲ��System.out.println����������ݣ�����PHP����û��
 * ��������û���ҵ��ɡ�����������һ��������һ�����Ʒ������������Լ�
 * д��һ�£��������Լ���һ��������Ƶ�PHP��Ŀ������ΪEC4P��
 * EC4P����Eclipse Console for PHP
 * ��ӭ���ʹ�ã����ṩ������֮�� ��
 ****************************************
 * edit soolly
 * vision��0.6 
 * date��2011-09-01
 * �Ľ���
 * 1�����ַ�������<br />,&nbsp;,\n,\r\nʱ�������������
 * 2���Ľ���һЩ�������⵼�µ�����
 * php�����������
 * �л��ῴ������дPHP�������չ
 * �����ϵĴ��룬�������߳��������ڽ�����������ӡ��ʱ��˳�򲻻�����
 * һ��ʱ���Ժ�ƻ�����eclipse���
 * ���뷢����http://club.topsage.com/thread-2450808-1-1.html
 *****************************************
 * edit soolly
 * vision��0.7 
 * date��2011-11-23
 * 
 * 1����д��������Ű淽������������Ƚ�
 * 2�������Զ����������ţ����Ը������Լ�ϰ�ߵķ�ʽ�鿴���
 * 3��ȥ����һЩ�����õķ���
 * 4���޸���һЩ�ط������ȱ��
 */
class ec4p{
	public function __construct(){
		self::init();
	}
	public static function init(){
		require_once 'ec4p_config.php';
		if(!defined('CHARSET')){//����������ļ�����ȷ��CHARSET��ֵ������CHARSET�Ѿ�ָ��
	    	define('CHARSET',self::get_charset(file_get_contents($_SERVER['SCRIPT_FILENAME'])));
	    }
		
	    if(extension_loaded('xdebug')){
    		if(IS_XDEBUG_USER){//�Ƿ�ʹ��xdebug
    		    define('IS_XDEBUG' , TRUE);
    		}else{
    		    define('IS_XDEBUG' , FALSE);
    	    }
        }else{
    		define('IS_XDEBUG' , FALSE);
    	}
	    
	    //�ı�Ĭ������չ����ʽ
	    if(!defined('DEFAULTREMARK')){
	    	define('DEFAULTREMARK' , REMARK);
	    }
	    
	    if(!defined('TREE_ENABLE')){
			define('TREE_ENABLE', false);
		}
		
	}

    /**************************
     *
     *       �����  �ຯ��
     *
     **************************/

    /**
     * ҳ����������У�����̨ÿ�����������
     * @param $s �ַ���
     * @param $arr ����
     */
    public static function echo_p(){
    	self::init();
    	$arg_list = func_get_args();
    	return call_user_func_array("self::echo_op",array($arg_list,false));
    }

    /**
     * ҳ��������У�����̨ÿ���������
     * @param $s �ַ���
     * @param $arr ����
     */
    public static function echobr_p(){
    	self::init();
    	$arg_list = func_get_args();
    	return call_user_func_array("self::echo_op",array($arg_list,true));
    }
   	/**
   	 * ���ַ������'<br />'
   	 * @param string $s
   	 * @param bool $br
   	 * @param bool $enableXdebug
   	 * @return void
   	 */
    public static function echobr($s,$br = TRUE,$enableXdebug = TRUE){
    	self::init();
    	return self::echobr_op($s,$br,$enableXdebug);
    }

    /**
     * ��ҳ�����������̨ÿ�����������
     * @param $s �ַ���
     * @param $arr ����
     */
    public static function println(){
    	self::init();
    	$arg_list = func_get_args();
    	return call_user_func_array("self::println_op",array($arg_list,array(1,3)));
    }

    /**
     * ��������λ�õ���������Ϣ��������ֻ�����Լ��Ĳ���
     * @param $func_stack_deep �������
     * @param $_stackparams �Ƿ���ʾ����������������
     * ��������Ƿ�ɹ�
     */
    public static function bugthis($func_stack_deep = 'MAX',$_stackparams=TRUE){
    	self::init();
    	if(!CONSOLE_ENABLE) return '';
    	if(!IS_XDEBUG)
    		self::set_errmsg('��������Ҫxdebug��չ֧�֣�����PHP�п���xdebug');
    	$arr = self::func_stack($func_stack_deep);
    	array_shift($arr);
    	$str = self::ffunc_stack($arr,$_stackparams);
    	return self::printconsole("----------  BUGTHIS  -----------",$str);
    }
    /*************************
     *
     *     ������ �ຯ��
     *
     *************************/

    /**
     * �����̨�����Ķ˿����
     * @param $s �ַ���
     * ���� �ɹ�д�����ĳ��ȣ����󷵻�false
     */
    private static function printconsole($s,$stack = NULL){
        $s .= $stack === NULL ? $_SERVER['SCRIPT_FILENAME']:self::_iconv($stack);
        
        if( P_ERROR === TRUE && count(self::set_errmsg()) != 0){
        	$e = "\r\n".self::_iconv(self::array2str(self::set_errmsg(),true,NORMAL));
        	if(strtoupper(CHARSET) === 'UTF-8' && self::get_charset($e) === 'GBK'){
	    		if(extension_loaded('iconv') === TRUE){
	    			if(self::typeof($e) === STRING){
	    				$s .= iconv('GBK', OUT_CON_CHARSET, $e);
	    			}
	    		}else{
	    			$e = 'extension<iconv> is not loaded!';
	    			self::set_errmsg($e);
	    			$s = $e."\r\n".$s;
	    		}
	    	}
	    	$s .= $e;
        }
        
        //$s .= runtimeerror();
        $s = self::escapeS_EOF($s);
        $in = $s . "\r\n" .S_EOF;
     	$socket = self::getSocket();
     	if($socket === FALSE){
     		return FALSE;
     	}else{
     		$writelen = @socket_write($socket, $in, strlen($in));
     		socket_close($socket);
     		if($writelen === FALSE){
     			return FALSE;
     		}else{
     			return $writelen;
     		}
     	}
    }
    
    
    /**
     * ����socket���� 
     * ���󷵻�false
     */
    private static function getSocket(){
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($socket === FALSE) {
            self::set_errmsg('socket����ʧ��: ԭ��: ' . socket_strerror(socket_last_error()));
            return FALSE;
        } else {
            //echobr( " socket ����OK.");
        }
        $result = @socket_connect($socket, ADDRESS, SERVICE_PORT);
        if ($result === FALSE) {
            self::set_errmsg( 'socket����ʧ�ܡ� <br/> ԭ��: ($result) ' . socket_strerror(socket_last_error($socket)));
            return FALSE;
        } else {
            //echobr( "socket ���� OK.");
        }
        return $socket;
    }

    private static function escapeS_EOF($str){
    	if(preg_match("/(.*\n*)(".S_EOF.")(\n*.*)/", $str)){
    		$str=preg_replace("/(.*\n*)(".S_EOF.")(\n*.*)/","$1\\".S_EOF."$3", $str);
    	}
    	return $str;
    }
    /*************************
     *
     *     �����ǹ����ຯ��
     *
     *************************/
    
    /**
     * ����̨��������ú�����������
     */
    private static function println_op(){
    	if(!CONSOLE_ENABLE) return '';
    	$arg_list = func_get_args();
    	$str = '';
    	foreach ($arg_list[0] as $arg){
    		$str .= self::_2str($arg)."\r\n";
    	}
    	if(self::set_func_page()){
		    $fs = self::func_stack($arg_list[1][0],$arg_list[1][1]);
		    return self::printconsole(self::_iconv($str),self::ffunc_stack($fs,false));
    	}else{
		    return self::printconsole(self::_iconv($str),'');
    	}
    }
    
    /**
     * ҳ����������ú�����������
     */
    private static function echo_op(){
    	$arg_list = func_get_args();
    	foreach ($arg_list[0] as $arg){
    		if(!($arg===NULL))self::echobr_op($arg,$arg_list[1]);
    	}
    	if(self::set_func_page()){
		    $fs = self::func_stack(1,3);
		    if(!($fs===NULL))self::echobr_op(self::ffunc_stack($fs,false),true,false);
    	}
    	self::echobr_op(self::array2str(self::set_errmsg(),false,NORMAL),true,false);
    	if(!CONSOLE_ENABLE) return '';
    	return call_user_func_array("self::println_op",array($arg_list[0],array(1,5)));
    }
    
    /**
     * ���ַ������'<br />'
     * @param $s �ַ��� string
     * @param $br �Ƿ��� Ĭ��true
     * �޷���  void
     */
    private static function echobr_op($s,$br = TRUE,$enableXdebug = TRUE){
    	(!headers_sent()) && header('Content-Type:text/html; charset='.CHARSET);
    	if(self::typeof($s) === STRING){
	    	if(strtoupper(CHARSET) === 'UTF-8' && self::get_charset($s) === 'GBK'){
	    		if(extension_loaded('iconv') === TRUE){
	    			$s = iconv('GBK', OUT_CON_CHARSET, $s);
	    		}else{
	    			$e = 'extension<iconv> is not loaded!';
	    			self::set_errmsg($e);
	    			$s = $e.'<br />'.$s;
	    		}
	    	}
    	}
    	if(IS_XDEBUG===TRUE && $enableXdebug===TRUE){
    		var_dump($s);
    	}else{
    		if($br){
    			echo $s.'<br />';
    		}else{
    			echo $s.'';
    		}
    	}
    }
    
	/**
     * �����Ƿ�������������ļ�����Ϣ
     * 
     * @param bool $enable
     */
    public static function set_func_page($enable = ''){
    	self::init();
    	static $func_page_enable = true;
    	if($enable !== ''){
    		if($enable == true){
    			return $func_page_enable = true; 
    		}else{
    			return $func_page_enable = false;
    		}
    	}else{
    		return $func_page_enable;
    	}
    }
    /**
     * ת���ַ�������  ��UTF-8
     * @param $s �ַ��� string
     * ���� �ַ��� string
     */
    private static function _iconv($s){
    	//���������ͣ�ȷ���Ƿ�ת������
    	if(strtoupper(CHARSET) === 'UTF-8' || self::get_charset($s) === 'UTF-8')
    		return $s;
    	if(extension_loaded('iconv') === TRUE)
    		return iconv(CHARSET, OUT_CON_CHARSET, $s);
    	return self::set_errmsg('extension<iconv> is not loaded!');
    }
    
    /**
     * ��ʽ����string��string
     * ���������$obj�����û��Զ����class���ص���ʲô��
     * @param $obj ��������
     * ���� �ַ��� string
     */
    private static function _2str($obj,$console=true,$remark=DEFAULTREMARK){
    	if($remark === REMARK || $remark === NORMAL || $remark === FORMAT){
	    	if(self::typeof($obj)===UNKNOWN) return self::set_errmsg('����_2str()�еĲ���$obj����: '.UNKNOWN);
	    	if(self::typeof($obj)===IS_NULL) return '';
	    	if(self::typeof($obj)===STRING) return $obj;
	    	if(is_array($obj)){
	    		return self::array2str($obj,$console,$remark);
	    	}else{
	    		$val = self::typeof($obj)=== BOOL ?($obj?'ture':'false'):$obj;
	    	//���������$obj�����û��Զ����class���ص���ʲô��
	    		return (self::typeof($obj)=== OBJECT ?get_class($obj):''.$val);
	    	}
    	}else{
    		return self::set_errmsg('���� DEFAULTREMARK['.DEFAULTREMARK.'] ERROR!');
    	}
    }
    
    /**
     * ת�����鵽�ַ���  ���ƺ���
     * @param $arr ����
     * ���� string
     */
    private static function array2str($arr,$console=true,$remark=DEFAULTREMARK){
        if(!is_array($arr)){
    		return ''.$arr;
    	}else{
	    	if($remark === FORMAT){
	    		return self::farr2arrstr($arr);
	    	}
    		$str = self::farr2str(array($arr),$console,$remark);
	    	//$str .= "<br />\n";
	    	return $str;
    	}
    }

    /**
     * ��ʽ�����鵽�ַ���
     * @param $arr ���� array
     * @param $remark ���� string
     * @param $deep ��ֵ��������ȣ�ϵͳ�Զ���� int
     * @param $str ϵͳ�Զ���ӣ�ͬʱ���ڷ���  string
     * ���� �ַ��� string
     */
    private static function farr2str($arr,$console=true,$remark=DEFAULTREMARK,$deep=0,$str=''){
    	foreach($arr as $k => $v){
    		$inden = $console?CONSOLE_INDENTATION:PAGE_INDENTATION;
    		if(is_array($v)){
    			$str .= $console?"\r\n":"<br />";
    			for($i=0;$i<=$deep;$i++){
    				if(TREE_ENABLE){
    					$str .= $inden.(($i==$deep)?ARRAY_NODE:TREE);
    				}else{
    					$str .= $inden;
    				}
    			}
    			if($remark === REMARK){
    				$str .= '<'.($deep-1).'>['.self::farrkv($k,FALSE).'] => (array > '.count($v).') ';
    			}elseif($remark === NORMAL){
    				$str .= self::farrkv($k,FALSE).' => ';
    			}
    			$deep = $deep+1;
    			$str = self::farr2str($v,$console,$remark,$deep,$str);
    			$deep = $deep-1;
    		}else{
    			$str .= $console?"\r\n":"<br />";
    			for($i=0;$i<=$deep;$i++){
    				if(TREE_ENABLE){
    					$inden = ($i == $deep)?LEAF_NODE:$inden;
    					$str .= $inden.(($i==$deep)?'':TREE);
    				}else{
    					$str .= $inden;
    				}
    			}
    			if($remark === REMARK){
    				$str .= '<'.($deep-1).'>['.self::farrkv($k,FALSE).'] => ('.self::typeof($v).') '.self::farrkv($v);
    			}elseif($remark === NORMAL){
    				//�������� ���� �� ��Դ�������л�
    				$str .= self::farrkv($k,FALSE).' => '.self::farrkv($v);
    			}
    		}
    	}
    	return $str;
    }

    /**
     * ��ʽ�����鵽���鴴���ַ���
     * @param $arr ���� array
     * @param $deep ��ֵ��������ȣ�ϵͳ�Զ���� int
     * @param $str ϵͳ�Զ���ӣ�ͬʱ���ڷ���  string
     * ���� �ַ��� string
     */
    private static function farr2arrstr($arr,$deep=0){
    	$str = '';
    	$ki = 0;
    	foreach($arr as $k => $v){
    		$ki++;
    		$d = $ki == count($arr)?'':',';
    		if(is_array($v)){
    			$str .= self::farrkv($k,FALSE).' => array(';
    			$deep++;
    			$str .= self::farr2arrstr($v,$deep);
    			$deep--;
    			$str .= '),';
    		}else{
    			//$v : �ж����$v��string�Ļ�������������ַ���һ��ת��
    			$str .= self::farrkv($k,FALSE).' => '.self::farrkv($v);
    			$str .= $d;
    		}
    	}
    	if($deep==0)
    	{
    		$str = 'array('.$str.')';
    	}
    	return $str;
    }
    
    /**
     * 
     * ��ʽ����������
     * @param unknown_type $arr
     * @param unknown_type $deep
     */
    private static function farr2funcargs($arr){
    	$str = '';
    	$arr_len = count($arr);
    	$i = 1;
    	foreach($arr as $k => $v){
			$str .= $v ;
			if($i == $arr_len)break;
			$str .= ', ' ;
			$i++;
    	}
    	return $str;
    }
    
    /**
     * �������м�ֵ�Լ�����Ӧ�� ����(') ת��booleanΪ�ַ���(true|false)�ȵ�
     * @param $arg �������� ����
     * @param $is_v �Ƿ��� ֵ value
     */
    private static function farrkv($arg='',$is_v=TRUE){
        if($is_v === FALSE){
	    	if(self::typeof($arg)=== STRING || self::typeof($arg)=== NUMERIC ){
	    		$arg = '\''.$arg.'\'';
	    	}
	    	return $arg;
        }
        if($is_v === TRUE){
        	$v = $arg;
	    	if(self::typeof($v)=== STRING || self::typeof($v)=== NUMERIC ){
	    		return '\''.$v.'\'';
	    	}
	        if(self::typeof($v)=== BOOL){
	        	return $v?'ture':'false';
	    	}
	    	if(self::typeof($v)=== IS_NULL){
	    		return IS_NULL;
	    	}
	    	if(self::typeof($v)=== RESOURCE || self::typeof($v)=== OBJECT){
	    		return '('.self::get_obj_info($v).')';
	    	}
	    	return $v;
        }
    }

    /**
     * ��ȡ�Ǳ���scalar�Լ�������array���͵���Ϣ
     * ��ʱ������ȡclass name and resource
     * @param $obj
     * ���� �ַ��� string ���󷵻�false
     */
    private static function get_obj_info($obj){
    	if(self::typeof($obj) === OBJECT){
    		return get_class($obj);//���� �� ����
    	}
    	if(self::typeof($obj)=== RESOURCE){
    		return get_resource_type($obj).','.$obj;//������Դ����
    	}
    	return FALSE;
    }
    
    /**
     * ��ȡ����$arg�ı���
	 * ȫ�����Ϊ GBK UTF-8
     * @param $str �ַ���
     */
    private static function get_charset($str){
    	//extension_loaded('mbstring');
    	$str_charset = '';
    	if(self::typeof($str) !== STRING){
			self::set_waring('private static function: get_charset��������Ϊstring��');
    		$str = strval($str);
    	}

    	switch(strtoupper(mb_detect_encoding($str,array('ASCII','UTF-8','GB2312','GBK','BIG5')))){
    		case 'CP936':
    			//cp936:GBK
    			$str_charset = 'GBK';
    			break;
    		case 'GBK':
    			$str_charset = 'GBK';
    			break;
    		case 'EUC-CN':
    			//EUC-CN:GB2312
    			$str_charset = 'GBK';
    			break;
    		case 'GB2312':
    			//EUC-CN:GB2312
    			$str_charset = 'GBK';
    			break;
    		case 'UTF-8':
    			$str_charset = 'UTF-8';
    			break;
    		default:
    			$str_charset = 'GBK';
    			break;
    	}
		return $str_charset;
    }
    
   	/**
	 * V9 �ж��ַ����Ƿ�Ϊutf8���룬Ӣ�ĺͰ���ַ�����ture
	 * ��ʱû��ʹ��
	 * @param $string
	 * @return bool
	 */
	private static function OFF_is_utf8($string) {
		return preg_match('%^(?:
						[\x09\x0A\x0D\x20-\x7E] # ASCII
						| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
						| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
						| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
						| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
						| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
						| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
						| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
						)*$%xs', $string);
	}
    
    /**
     * ��ȡ ���� $var ������
     * @param $var ��������
     * ���� �ַ��� ����
     */
    private static function typeof($var){
    	if(is_null($var))		return IS_NULL;
		if(is_bool($var))    	return BOOL;

		if(is_float($var))    	return FLOAT;
		if(is_int($var))    	return INT;

		if(is_numeric($var))	return NUMERIC;
		if(is_string($var))    	return STRING;

		if(is_resource($var))	return RESOURCE;
		if(is_array($var))		return IS_ARRAY;
		if(is_object($var))    	return OBJECT;
		return UNKNOWN;
    }
    
    /**
     * ���ر���Ŀ����Ա��Ϣ
     */
    public static function get_admin(){
    	return '[mailto:boyjipc@gmail.com]';
    }

    /**
     * 
     * ��ʱû��ʹ��
     * @param unknown_type $func_stack_deep
     * @param unknown_type $_stackparams
     */
    private static function setstack($func_stack_deep = 0,$_stackparams = NULL){
    	static $_func_stack_deep=0;
    	static $_sparams=NULL;
    	$_func_stack_deep = $func_stack_deep;
    	$_sparams = $_stackparams;
    	return array('func_stack_deep' => $_func_stack_deep,'stackparams' => $_sparams);
    }
    
    /**
     * ��ʽ�� �ű�ִ�й��� �Ա����̨���
     * @param $arr ׷�ٵ����
     * ���� �ַ��� string
     */
    private static function ffunc_stack($arr,$_stackparams = NULL){
    	if(!defined('STACKPARAMS')){
    		$stackparams = false;
    	}else{
    		$stackparams = true;
    	}
    	$strstack = "\r\n";
    	foreach($arr as $k => $v){
    		$strstack .= ' ['.$k.'] ';
	    	$ffs_function = array_key_exists('function',$v)?$v['function']:'';
	    	$ffs_class = array_key_exists('class',$v)?'@'.$v['class']:'';
	    	$ffs_file = array_key_exists('file',$v)?$v['file']:'';
	    	$ffs_line = array_key_exists('line',$v)?$v['line']:'';
	    	$ffs_params = array_key_exists('params',$v)?$v['params']:'';
	    	$strstack .= '#'
	    				.$ffs_line
	    				.' '
	    				.$ffs_function
	    				.'()'
	    				.$ffs_class
	    				.' '
	    				.$ffs_file
	    				."\r\n";
	    	if($_stackparams === NULL){
	    		$_stackparams = $stackparams;
	    	}
	    	if($_stackparams){
	    		$strstack .=	CONSOLE_FUNC_INDENTATION
	    						.'function '
	    						.$ffs_function
	    						."("
//	    						.array2str($ffs_params,REMARK)
//	    						.array2str($ffs_params,true,FORMAT)
								.self::farr2funcargs($ffs_params)
	    						.")\r\n"
	    						;
	    	}
    	}
		return $strstack;
    }

    public static function set_stack_start_addition($addition = -1){
    	self::init();
    	static $start_addition = 0;
    	if($addition !== null && intval($addition) >= 0){
    		$start_addition = intval($addition);
    	}
    	return $start_addition;
    	
    }
    /**
     * ׷�ٽű�ִ�й���
     * @param $deep ׷�ٵ����
     * �������ݸ�ʽ����Ϣkey=0ָ����ñ������ķ����ı�������Ϣ
     * keyΪ����ʱ��ָ��main��ڵ���һ����������Ϣ
     */
    private static function func_stack($deep=0,$start=1){
    	if(!IS_XDEBUG){	
    		self::set_errmsg('��������Ҫxdebug��չ֧�֣�����PHP�п���xdebug!');
    		$v = array(array());
    		return $v;
    	}
    	$arr = xdebug_get_function_stack();
    	$start += self::set_stack_start_addition();
    	if($start < 0 )$start = 1;
    	for($i = 0; $i < $start; $i++ ){
    		array_pop($arr);
    	}
    	array_shift($arr);
    	if($deep === 'MAX' || $deep === 'max'){
    		$deep = count($arr);
    	}else{
	    	if(defined('FUNC_STACK_DEEP')){
	    		if(self::typeof($deep) === INT){
	    			if($deep === 0){
		    			if(FUNC_STACK_DEEP === 'MAX'){
		    				$deep = count($arr);
		    			}else{
		    				if(self::typeof(FUNC_STACK_DEEP) === INT){
		    					$deep = FUNC_STACK_DEEP;
		    					if($deep < 0){
	    							self::set_errmsg('���� FUNC_STACK_DEEP['.FUNC_STACK_DEEP.'] ����Ϊ������(+int) ���� �ַ��� (string)[\'MAX\']');
	    						}
		    				}else{
		    					self::set_errmsg('���� FUNC_STACK_DEEP['.FUNC_STACK_DEEP.'] ����Ϊ������(+int) ���� �ַ��� (string)[\'MAX\']');
		    				}
		    			}
	    			}elseif($deep < 0){
	    				self::set_errmsg('���� $deep['.$deep.'] ����Ϊ������(+int)');
	    			}
	    		}else{
	    			self::set_errmsg('���� $deep['.$deep.'] ����Ϊ������(+int)');
	    			return null;
	    		}
	    	}
    	}
    	if(count($arr)===0){
    		self::set_errmsg('���� xdebug_get_private static function_stack() ����ֵ [0] ��ȷ���Դ����������������EC4P'.self::get_admin().'��лл��');
    		return $arr;
    	}
    	$a = array();
    	if($deep >0){
    		if($deep>count($arr))$deep = count($arr);
    		for($i = 0;$i < $deep; $i++){
    			$a[$i] = array_pop($arr);
    		}
    	}else{
    		$a[0] = array_pop($arr);
    	}
        return $a;
    }

    /**
     * ���ô�������
     * @param $err
     * ���� $errmsg
     */
    private static function set_errmsg($err = ''){
    	static $errmsg = array();
    	static $errcount = 0;
    	if(!($err === '')){
    		$errcount++;
    		$errmsg[] = 'E['.$errcount.'] : '.$err;
    	}
    	return ($errmsg);
    }
    
    /**
     * ���ô�������
     * @param $err
     * ���� $errmsg
     */
    private static function set_waring($war = ''){
    	static $warmsg = array();
    	static $warcount = 0;
    	if(!($war === '')){
    		$warcount++;
    		$warmsg[] = 'E['.$warcount.'] : '.$war;
    	}
    	return ($warmsg);
    }

    /**
     * ��¼����ʱ�����Ĵ���  ��ʱû��ʹ��
     * @param $err
     */
    private static function runtimeerror($err = ''){
    	static $serr = '';
    	$serr .= self::_2str($err)."<br />\n";
    	if($serr === '')return '';
    	return $serr;
    }

    private static function _test($deep=0){
    	$arr = self::func_stack($deep);
    	echo self::ffunc_stack($arr);
    	echobr($arr);
    }

    //����xdebug����
    private static function fix_string1($a)
    {
        echo "Called @ ".
            xdebug_call_file().
            ":".
            xdebug_call_line().
            " from ".
            xdebug_call_function();
    }

//var_dump(fix_string(array('Derick')));
}

/**
     * ҳ����������У�����̨ÿ�����������
     * @param $s �ַ���
     * @param $arr ����
     */
    function echo_p(){
    	$arg_list = func_get_args();
    	return call_user_func_array("ec4p::echo_p",$arg_list);
    }

    /**
     * ҳ��������У�����̨ÿ���������
     * @param $s �ַ���
     * @param $arr ����
     */
    function echobr_p(){
    	$arg_list = func_get_args();
    	return call_user_func_array("ec4p::echobr_p",$arg_list);
    }
   	/**
   	 * ���ַ������'<br />'
   	 * @param string $s
   	 * @param bool $br
   	 * @param bool $enableXdebug
   	 * @return void
   	 */
    function echobr($s,$br = TRUE,$enableXdebug = TRUE){
    	return ec4p::echobr_op($s,$br,$enableXdebug);
    }

    /**
     * ��ҳ�����������̨ÿ�����������
     * @param $s �ַ���
     * @param $arr ����
     */
    function println(){
    	set_stack_start_addition(2);
    	$arg_list = func_get_args();
    	return call_user_func_array("ec4p::println",$arg_list);
    }

    /**
     * ��������λ�õ���������Ϣ��������ֻ�����Լ��Ĳ���
     * @param $func_stack_deep �������
     * @param $_stackparams �Ƿ���ʾ����������������
     * ��������Ƿ�ɹ�
     */
    function bugthis($func_stack_deep = 'MAX',$_stackparams=TRUE){
    	set_stack_start_addition(1);
    	return ec4p::bugthis($func_stack_deep,$_stackparams);
    }
    
    function set_func_page($enable = ''){
    	return ec4p::set_func_page($enable);
    }
    function set_stack_start_addition($addition = null){
    	return ec4p::set_stack_start_addition($addition);
    }
?>
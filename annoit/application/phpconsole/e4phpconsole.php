<?php
	require_once 'ec4p_config.php';
/**
 **************************************
 * EC4P
 * 我是一个JAVA转来的PHP程序员，习惯了Windows下的eclipse使用
 * 调试的时候喜欢System.out.println输出调试内容，但是PHP好似没有
 * 或许是我没有找到吧。在网上找了一翻，翻出一个半成品的替代方案。自己
 * 写了一下，做出了自己第一个相对完善的PHP项目，定名为EC4P。
 * EC4P：即Eclipse Console for PHP
 * 欢迎大家使用，并提供不完善之处 。
 ****************************************
 * edit soolly
 * vision：0.6 
 * date：2011-09-01
 * 改进：
 * 1、当字符串中有<br />,&nbsp;,\n,\r\n时，输出出现乱码
 * 2、改进了一些编码问题导致的乱码
 * php程序引入代码
 * 有机会看看怎样写PHP程序的扩展
 * 网络上的代码，完善了线程锁，现在接收数据流打印的时候顺序不会乱了
 * 一段时间以后计划做成eclipse插件
 * 代码发布在http://club.topsage.com/thread-2450808-1-1.html
 *****************************************
 * edit soolly
 * vision：0.7 
 * date：2011-11-23
 * 
 * 1、改写了输出的排版方法，程序更加稳健
 * 2、增加自定义缩进符号，可以更符合自己习惯的方式查看输出
 * 3、去掉了一些不常用的方法
 * 4、修改了一些地方的设计缺陷
 */

    if(!defined('CHARSET')){//根据输入的文件内容确定CHARSET的值，除非CHARSET已经指定
    	define('CHARSET',get_charset(file_get_contents($_SERVER['SCRIPT_FILENAME'])));
    }
    
    if(extension_loaded('xdebug')){
		if(IS_XDEBUG_USER){//是否使用xdebug
		    define('IS_XDEBUG' , TRUE);
		}else{
		    define('IS_XDEBUG' , FALSE);
	    }
    }else{
		define('IS_XDEBUG' , FALSE);
	}
    
    //改变默认数组展开形式
    if(!defined('DEFAULTREMARK')){
    	define('DEFAULTREMARK' , REMARK);
    }
    
    if(!defined('TREE_ENABLE')){
		define('TREE_ENABLE', false);
	}

    /**************************
     *
     *       总入口  类函数
     *
     **************************/

    /**
     * 页面输出不换行，控制台每个输出都换行
     * @param $s 字符串
     * @param $arr 数组
     */
    function echo_p(){
    	$arg_list = func_get_args();
    	return call_user_func_array("echo_op",array($arg_list,false));
    }

    /**
     * 页面输出换行，控制台每个输出换行
     * @param $s 字符串
     * @param $arr 数组
     */
    function echobr_p(){
    	$arg_list = func_get_args();
    	return call_user_func_array("echo_op",array($arg_list,true));
    }
   	/**
   	 * 给字符串添加'<br />'
   	 * @param string $s
   	 * @param bool $br
   	 * @param bool $enableXdebug
   	 * @return void
   	 */
    function echobr($s,$br = TRUE,$enableXdebug = TRUE){
    	return echobr_op($s,$br,$enableXdebug);
    }

    /**
     * 无页面输出，控制台每个输出都换行
     * @param $s 字符串
     * @param $arr 数组
     */
    function println(){
    	$arg_list = func_get_args();
    	return call_user_func_array("println_op",array($arg_list,array(1,3)));
    }

    /**
     * 调试所在位置的上下文信息，本函数只依赖自己的参数
     * @param $func_stack_deep 调试深度
     * @param $_stackparams 是否显示所处函数参数详情
     * 返回输出是否成功
     */
    function bugthis($func_stack_deep = 'MAX',$_stackparams=TRUE){
    	if(!CONSOLE_ENABLE) return '';
    	if(!IS_XDEBUG)
    		set_errmsg('本功能需要xdebug扩展支持，请在PHP中开启xdebug');
    	$arr = func_stack($func_stack_deep);
    	//array_shift($arr);
    	$str = ffunc_stack($arr,$_stackparams);
    	return printconsole("----------  BUGTHIS  -----------",$str);
    }
    /*************************
     *
     *     流处理 类函数
     *
     *************************/

    /**
     * 向控制台监听的端口输出
     * @param $s 字符串
     * 返回 成功写入流的长度，错误返回false
     */
    function printconsole($s,$stack = NULL){
        $s .= $stack === NULL ? $_SERVER['SCRIPT_FILENAME']:_iconv($stack);
        
        if( P_ERROR === TRUE && count(set_errmsg()) != 0){
        	$e = "\r\n"._iconv(array2str(set_errmsg(),true,NORMAL));
        	if(strtoupper(CHARSET) === 'UTF-8' && get_charset($e) === 'GBK'){
	    		if(extension_loaded('iconv') === TRUE){
	    			if(typeof($e) === STRING){
	    				$s .= iconv('GBK', OUT_CON_CHARSET, $e);
	    			}
	    		}else{
	    			$e = 'extension<iconv> is not loaded!';
	    			set_errmsg($e);
	    			$s = $e."\r\n".$s;
	    		}
	    	}
	    	$s .= $e;
        }
        
        //$s .= runtimeerror();
        $s = escapeS_EOF($s);
        $in = $s . "\r\n" .S_EOF;
     	$socket = getSocket();
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
     * 返回socket对象 
     * 错误返回false
     */
    function getSocket(){
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($socket === FALSE) {
            set_errmsg('socket创建失败: 原因: ' . socket_strerror(socket_last_error()));
            return FALSE;
        } else {
            //echobr( " socket 创建OK.");
        }
        $result = @socket_connect($socket, ADDRESS, SERVICE_PORT);
        if ($result === FALSE) {
            set_errmsg( 'socket连接失败。 <br/> 原因: ($result) ' . socket_strerror(socket_last_error($socket)));
            return FALSE;
        } else {
            //echobr( "socket 连接 OK.");
        }
        return $socket;
    }

    function escapeS_EOF($str){
    	if(preg_match("/(.*\n*)(".S_EOF.")(\n*.*)/", $str)){
    		$str=preg_replace("/(.*\n*)(".S_EOF.")(\n*.*)/","$1\\".S_EOF."$3", $str);
    	}
    	return $str;
    }
    /*************************
     *
     *     以下是工具类函数
     *
     *************************/
    
    /**
     * 控制台输出被调用函数，不对外
     */
    function println_op(){
    	if(!CONSOLE_ENABLE) return '';
    	$arg_list = func_get_args();
    	$str = '';
    	foreach ($arg_list[0] as $arg){
    		$str .= _2str($arg)."\r\n";
    	}
    	if(set_func_page()){
		    $fs = func_stack($arg_list[1][0],$arg_list[1][1]);
		    return printconsole(_iconv($str),ffunc_stack($fs,false));
    	}else{
		    return printconsole(_iconv($str),'');
    	}
    }
    
    /**
     * 页面输出被调用函数，不对外
     */
    function echo_op(){
    	$arg_list = func_get_args();
    	foreach ($arg_list[0] as $arg){
    		if(!($arg===NULL))echobr_op($arg,$arg_list[1]);
    	}
    	if(set_func_page()){
		    $fs = func_stack(1,3);
		    if(!($fs===NULL))echobr_op(ffunc_stack($fs,false),true,false);
    	}
    	echobr_op(array2str(set_errmsg(),false,NORMAL),true,false);
    	if(!CONSOLE_ENABLE) return '';
    	return call_user_func_array("println_op",array($arg_list[0],array(1,5)));
    }
    
    /**
     * 给字符串添加'<br />'
     * @param $s 字符串 string
     * @param $br 是否换行 默认true
     * 无返回  void
     */
    function echobr_op($s,$br = TRUE,$enableXdebug = TRUE){
    	(!headers_sent()) && header('Content-Type:text/html; charset='.CHARSET);
    	if(typeof($s) === STRING){
	    	if(strtoupper(CHARSET) === 'UTF-8' && get_charset($s) === 'GBK'){
	    		if(extension_loaded('iconv') === TRUE){
	    			$s = iconv('GBK', OUT_CON_CHARSET, $s);
	    		}else{
	    			$e = 'extension<iconv> is not loaded!';
	    			set_errmsg($e);
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
     * 设置是否输出方法所在文件的信息
     * 
     * @param bool $enable
     */
    function set_func_page($enable = ''){
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
     * 转化字符串编码  到UTF-8
     * @param $s 字符串 string
     * 返回 字符串 string
     */
    function _iconv($s){
    	//检测编码内型，确定是否转换编码
    	if(strtoupper(CHARSET) === 'UTF-8' || get_charset($s) === 'UTF-8')
    		return $s;
    	if(extension_loaded('iconv') === TRUE)
    		return iconv(CHARSET, OUT_CON_CHARSET, $s);
    	return set_errmsg('extension<iconv> is not loaded!');
    }
    
    /**
     * 格式化非string到string
     * 隐患：如果$obj不是用户自定义的class返回的是什么？
     * @param $obj 任意类型
     * 返回 字符串 string
     */
    function _2str($obj,$console=true,$remark=DEFAULTREMARK){
    	if($remark === REMARK || $remark === NORMAL || $remark === FORMAT){
	    	if(typeof($obj)===UNKNOWN) return set_errmsg('方法_2str()中的参数$obj类型: '.UNKNOWN);
	    	if(typeof($obj)===IS_NULL) return '';
	    	if(typeof($obj)===STRING) return $obj;
	    	if(is_array($obj)){
	    		return array2str($obj,$console,$remark);
	    	}else{
	    		$val = typeof($obj)=== BOOL ?($obj?'ture':'false'):$obj;
	    	//隐患：如果$obj不是用户自定义的class返回的是什么？
	    		return (typeof($obj)=== OBJECT ?get_class($obj):''.$val);
	    	}
    	}else{
    		return set_errmsg('常量 DEFAULTREMARK['.DEFAULTREMARK.'] ERROR!');
    	}
    }
    
    /**
     * 转化数组到字符串  控制函数
     * @param $arr 数组
     * 返回 string
     */
    function array2str($arr,$console=true,$remark=DEFAULTREMARK){
        if(!is_array($arr)){
    		return ''.$arr;
    	}else{
	    	if($remark === FORMAT){
	    		return farr2arrstr($arr);
	    	}
    		$str = farr2str(array($arr),$console,$remark);
	    	//$str .= "<br />\n";
	    	return $str;
    	}
    }

    /**
     * 格式化数组到字符串
     * @param $arr 数组 array
     * @param $remark 常量 string
     * @param $deep 键值对所在深度，系统自动添加 int
     * @param $str 系统自动添加，同时用于返回  string
     * 返回 字符串 string
     */
    function farr2str($arr,$console=true,$remark=DEFAULTREMARK,$deep=0,$str=''){
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
    				$str .= '<'.($deep-1).'>['.farrkv($k,FALSE).'] => (array > '.count($v).') ';
    			}elseif($remark === NORMAL){
    				$str .= farrkv($k,FALSE).' => ';
    			}
    			$deep = $deep+1;
    			$str = farr2str($v,$console,$remark,$deep,$str);
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
    				$str .= '<'.($deep-1).'>['.farrkv($k,FALSE).'] => ('.typeof($v).') '.farrkv($v);
    			}elseif($remark === NORMAL){
    				//将来将对 对象 类 资源进行序列化
    				$str .= farrkv($k,FALSE).' => '.farrkv($v);
    			}
    		}
    	}
    	return $str;
    }

    /**
     * 格式化数组到数组创建字符串
     * @param $arr 数组 array
     * @param $deep 键值对所在深度，系统自动添加 int
     * @param $str 系统自动添加，同时用于返回  string
     * 返回 字符串 string
     */
    function farr2arrstr($arr,$deep=0){
    	$str = '';
    	$ki = 0;
    	foreach($arr as $k => $v){
    		$ki++;
    		$d = $ki == count($arr)?'':',';
    		if(is_array($v)){
    			$str .= farrkv($k,FALSE).' => array(';
    			$deep++;
    			$str .= farr2arrstr($v,$deep);
    			$deep--;
    			$str .= '),';
    		}else{
    			//$v : 判断如果$v是string的话将里面的特殊字符做一下转换
    			$str .= farrkv($k,FALSE).' => '.farrkv($v);
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
     * 格式化函数参数
     * @param unknown_type $arr
     * @param unknown_type $deep
     */
    function farr2funcargs($arr){
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
     * 给数组中键值对加上相应的 引号(') 转化boolean为字符串(true|false)等等
     * @param $arg 任意类型 参数
     * @param $is_v 是否是 值 value
     */
    function farrkv($arg='',$is_v=TRUE){
        if($is_v === FALSE){
	    	if(typeof($arg)=== STRING || typeof($arg)=== NUMERIC ){
	    		$arg = '\''.$arg.'\'';
	    	}
	    	return $arg;
        }
        if($is_v === TRUE){
        	$v = $arg;
	    	if(typeof($v)=== STRING || typeof($v)=== NUMERIC ){
	    		return '\''.$v.'\'';
	    	}
	        if(typeof($v)=== BOOL){
	        	return $v?'ture':'false';
	    	}
	    	if(typeof($v)=== IS_NULL){
	    		return IS_NULL;
	    	}
	    	if(typeof($v)=== RESOURCE || typeof($v)=== OBJECT){
	    		return '('.get_obj_info($v).')';
	    	}
	    	return $v;
        }
    }

    /**
     * 获取非标量scalar以及非数组array类型的信息
     * 暂时仅仅获取class name and resource
     * @param $obj
     * 返回 字符串 string 错误返回false
     */
    function get_obj_info($obj){
    	if(typeof($obj) === OBJECT){
    		return get_class($obj);//返回 类 名称
    	}
    	if(typeof($obj)=== RESOURCE){
    		return get_resource_type($obj).','.$obj;//返回资源类型
    	}
    	return FALSE;
    }
    
    /**
     * 获取参数$arg的编码
	 * 全部归结为 GBK UTF-8
     * @param $str 字符串
     */
    function get_charset($str){
    	//extension_loaded('mbstring');
    	$str_charset = '';
    	if(typeof($str) !== STRING){
			set_waring('function: get_charset参数必须为string型');
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
	 * V9 判断字符串是否为utf8编码，英文和半角字符返回ture
	 * 暂时没有使用
	 * @param $string
	 * @return bool
	 */
	function OFF_is_utf8($string) {
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
     * 获取 参数 $var 的类型
     * @param $var 任意类型
     * 返回 字符串 常量
     */
    function typeof($var){
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
     * 返回本项目管理员信息
     */
    function get_admin(){
    	return '[mailto:boyjipc@gmail.com]';
    }

    /**
     * 
     * 暂时没有使用
     * @param unknown_type $func_stack_deep
     * @param unknown_type $_stackparams
     */
    function setstack($func_stack_deep = 0,$_stackparams = NULL){
    	static $_func_stack_deep=0;
    	static $_sparams=NULL;
    	$_func_stack_deep = $func_stack_deep;
    	$_sparams = $_stackparams;
    	return array('func_stack_deep' => $_func_stack_deep,'stackparams' => $_sparams);
    }
    
    /**
     * 格式化 脚本执行过程 以便控制台输出
     * @param $arr 追踪的深度
     * 返回 字符串 string
     */
    function ffunc_stack($arr,$_stackparams = NULL){
    	if(!defined('STACKPARAMS')){
    		$stackparams = false;
    	}else{
    		$stackparams = true;
    	}
    	$strstack = "\r\n";
    	//var_dump($arr);
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
	    		$strstack .=	CONSOLE_INDENTATION
	    						.'function '
	    						.$ffs_function
	    						."("
//	    						.array2str($ffs_params,REMARK)
//	    						.array2str($ffs_params,true,FORMAT)
								.farr2funcargs($ffs_params)
	    						.")\r\n"
	    						;
	    	}
    	}
		return $strstack;
    }

    function set_stack_start_addition($addition = -1){
    	static $start_addition = 0;
    	if($addition !== null && intval($addition) >= 0){
    		$start_addition = intval($addition);
    	}
    	return $start_addition;
    }
    
    /**
     * 追踪脚本执行过程
     * @param $deep 追踪的深度
     * 返回数据格式的信息key=0指向调用本方法的方法的被调用信息
     * key为最大的时候指向main入口的下一个方法的信息
     */
    function func_stack($deep=0,$start=1){
    	if(!IS_XDEBUG){	
    		set_errmsg('本功能需要xdebug扩展支持，请在PHP中开启xdebug!');
    		$v = array(array());
    		return $v;
    	}
    	$arr = xdebug_get_function_stack();
    	$start += set_stack_start_addition();
    	if($start < 0 )$start = 1;
    	for($i = 0; $i < $start; $i++ ){
    		array_pop($arr);
    	}
    	array_shift($arr);
    	if($deep === 'MAX' || $deep === 'max'){
    		$deep = count($arr);
    	}else{
	    	if(defined('FUNC_STACK_DEEP')){
	    		if(typeof($deep) === INT){
	    			if($deep === 0){
		    			if(FUNC_STACK_DEEP === 'MAX'){
		    				$deep = count($arr);
		    			}else{
		    				if(typeof(FUNC_STACK_DEEP) === INT){
		    					$deep = FUNC_STACK_DEEP;
		    					if($deep < 0){
	    							set_errmsg('常量 FUNC_STACK_DEEP['.FUNC_STACK_DEEP.'] 必须为正整数(+int) 或者 字符串 (string)[\'MAX\']');
	    						}
		    				}else{
		    					set_errmsg('常量 FUNC_STACK_DEEP['.FUNC_STACK_DEEP.'] 必须为正整数(+int) 或者 字符串 (string)[\'MAX\']');
		    				}
		    			}
	    			}elseif($deep < 0){
	    				set_errmsg('参数 $deep['.$deep.'] 必须为正整数(+int)');
	    			}
	    		}else{
	    			set_errmsg('参数 $deep['.$deep.'] 必须为正整数(+int)');
	    			return null;
	    		}
	    	}
    	}
    	if(count($arr)===0){
    		set_errmsg('方法 xdebug_get_function_stack() 返回值 [0] 不确定性错误，请您将错误共享给EC4P'.get_admin().'。谢谢！');
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
     * 设置错误内容
     * @param $err
     * 返回 $errmsg
     */
    function set_errmsg($err = ''){
    	static $errmsg = array();
    	static $errcount = 0;
    	if(!($err === '')){
    		$errcount++;
    		$errmsg[] = 'E['.$errcount.'] : '.$err;
    	}
    	return ($errmsg);
    }
    
    /**
     * 设置错误内容
     * @param $err
     * 返回 $errmsg
     */
    function set_waring($war = ''){
    	static $warmsg = array();
    	static $warcount = 0;
    	if(!($war === '')){
    		$warcount++;
    		$warmsg[] = 'E['.$warcount.'] : '.$war;
    	}
    	return ($warmsg);
    }

    /**
     * 记录运行时产生的错误  暂时没有使用
     * @param $err
     */
    function runtimeerror($err = ''){
    	static $serr = '';
    	$serr .= _2str($err)."<br />\n";
    	if($serr === '')return '';
    	return $serr;
    }

    function _test($deep=0){
    	$arr = func_stack($deep);
    	echo ffunc_stack($arr);
    	echobr($arr);
    }

    //备用xdebug函数
    function fix_string1($a)
    {
        echo "Called @ ".
            xdebug_call_file().
            ":".
            xdebug_call_line().
            " from ".
            xdebug_call_function();
    }

//var_dump(fix_string(array('Derick')));

?>
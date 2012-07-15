<?php
include_once('application/phpconsole/e4phpconsole.php');

class file_model extends CI_Model{
	
	function create_config_php($conf_data){
		  //创建个日期
		// $timer1 = date("Y-m-d");
		$path_php = "resource_config/";
		$path_js = "resource_config/js_config/";
		$filename_php = $path_php.$conf_data['res_id'].".php";
		$filename_js = $path_js.$conf_data['res_id']."_conf.js";		
		$content_php = $this->get_php_config_str($conf_data);
		$content_js = $this->get_js_config_str($conf_data);

	    //先判断文件夹在不在
	    if(!file_exists($path_php)){
	    	//如果不存在生成这个目录,0777表示最大的读写权限
	    	if(mkdir($path_php,0777)){
	       		println("117  不能建立目录");
	       		exit();
	    	}
	   	}	  
		if(!file_exists($path_js)){
	    	//如果不存在生成这个目录,0777表示最大的读写权限
	    	if(mkdir($path_js,0777)){
	       		println("117  不能建立目录");
	       		exit();
	    	}
	   	}
	   //判断文件是否存在
	    if(!file_exists($filename_php)){
	    	//如果文件不存在,则创建文件
	    	@fopen($filename_php,"w");
	    }
	    if(!file_exists($filename_js)){
			@fopen($filename_js,"w");
	    }

	    //判断文件php是否可写
	    if(is_writable($filename_php)){
	      	//w:只写。打开并清空文件的内容；如果文件不存在，则创建新文件。
	      	if(!$handle =  fopen($filename_php,"w")){
	        	exit();
	      	}
	      	if(!fwrite($handle,$content_php)){
	        	exit();
	      	}
	      	//关闭文件流
	      	fclose($handle);
	     	println("生成文件并保存首次内容");
	    }else {
			return false;	    		
	   	}	   
		
	    //判断js文件是否可写
	    if(is_writable($filename_js)){
	      	//w:只写。打开并清空文件的内容；如果文件不存在，则创建新文件。
			if(!$handle =  fopen($filename_js,"w")){
	        	exit();
	      	}
	      	if(!fwrite($handle,$content_js)){
	        	exit();
	      	}
	      	//关闭文件流
	      	fclose($handle);
	     	println("生成文件并保存首次内容");
	    }else {
			return false;	    		
	   	}	   
	   	   
	}

	//生成资源对应的php配置，后台读取数据时载入
	function get_php_config_str($conf_data){
		$linefeed = "\r\n";
		$tab = "\t";
		$lt = $linefeed.$tab;
		
		$temp = var_export($conf_data['rtype'], true);
		
		$config_resource_table = 'resource';
		$config_node_logic_table = 'node_logic';		
		$config_unit_table = 'unit';		
		$config_task_table = 'task';		
		$config_node_table = 'node';
		$config_ke_table = $conf_data['ke_table'];		
		$config_relationship_table = 'relationship';
								
		$conf_str = '<?php';
			$conf_str .= $lt;
			$conf_str .= '$config_resource_table = \'' .$config_resource_table.'\' ;';						
			$conf_str .= $lt;
			$conf_str .= '$config_node_logic_table = \'' .$config_node_logic_table.'\' ;';		
			$conf_str .= $lt;
			$conf_str .= '$config_unit_table = \'' .$config_unit_table.'\' ;';						
			$conf_str .= $lt;
			$conf_str .= '$config_task_table = \'' .$config_task_table.'\' ;';		
			$conf_str .= $lt;
			$conf_str .= '$config_node_table = \'' .$config_node_table.'\' ;';						
			$conf_str .= $lt;
			$conf_str .= '$config_ke_table = \'' .$config_ke_table.'\' ;';		
			$conf_str .= $lt;
			$conf_str .= '$config_relationship_table = \'' .$config_relationship_table.'\' ;';									
			
			$conf_str .= $lt;
			$conf_str .= $lt;		
			$conf_str .= $lt;
			$conf_str .= '$config_res_title = \'' .$conf_data['resource_title'].'\' ;';		
			$conf_str .= $lt;
			$conf_str .= '$config_res_content = \'' .$conf_data['resContent'].'\' ;';
			$conf_str .= $lt;
			$conf_str .= '$config_annatate_mode = \'' .$conf_data['annatate_mode'].'\' ;';
			$conf_str .= $lt;
			$conf_str .= '$config_isLogic = \'' .$conf_data['isLogic'].'\' ;';
			$conf_str .= $lt;
			$conf_str .= '$config_logic_user = ' .$conf_data['logic_user'].' ;';
			$conf_str .= $lt;
			//$conf_str .= '$config_ntype = \'' .$conf_data['ntype'].'\' ;';	
			$conf_str .= '$config_ntype = ' .var_export($conf_data['ntype'], true).' ;';	
			$conf_str .= $lt;
			//$conf_str .= '$config_attachAttr = \'' .$conf_data['attachAttr'].'\' ;';	
			$conf_str .= '$config_attachAttr = ' .var_export($conf_data['attachAttr'],true).' ;';	
			$conf_str .= $lt;
			//$conf_str .= '$config_rtype = \'' .$conf_data['rtype'].'\' ;';	
			$conf_str .= '$config_rtype = ' .var_export($conf_data['rtype'],true).' ;';
			$conf_str .= $lt;
			$conf_str .= '$config_adjacentOffset = ' .$conf_data['adjacentOffset'].' ;';	
			
			
		$conf_str .= $linefeed;
		$conf_str .= '?>';
		return $conf_str;
	}

	//生成资源对应的js配置，前台标注时载入
	function get_js_config_str($conf_data){
		$linefeed = "\r\n";
		$tab = "\t";
		$lt = $linefeed.$tab;
		$conf_str = '';
		
		$conf_str .= 'var rtype = ' .json_encode($conf_data['rtype']).' ;';	
		$conf_str .= $linefeed;
		$conf_str .= 'var ntype = ' .json_encode($conf_data['ntype']).' ;';	
		$conf_str .= $linefeed;
		$conf_str .= 'var attachAttr = ' .json_encode($conf_data['attachAttr']).' ;';	
		$conf_str .= $linefeed;
		$conf_str .= 'var adjacentOffset = ' .json_encode($conf_data['adjacentOffset']).' ;';	
				
		return $conf_str;
	}
}

<?php
//数组展开形式
define('FORMAT' , 			'format');//格式化成创建数组的表达式
define('NORMAL' , 			'normal');//相对于REMARK，只是将数组打印出来
define('REMARK' , 			'remark');//添加值得类型所在的深度等类容，方便查看

//normal indentation
define('CONSOLE_INDENTATION', '~~~~');
define('CONSOLE_FUNC_INDENTATION', '    ');
define('PAGE_INDENTATION', '&nbsp;&nbsp;&nbsp;&nbsp;');

//tree
//define('TREE_ENABLE', true);
//tree indentation
define('ARRAY_NODE', '+');
define('LEAF_NODE', '-----');
define('TREE', '|');

//define('IS_XDEBUG_USER' , TRUE);//是否使用xdebug
define('CONSOLE_ENABLE', true);//控制台输出是否可行
define('P_ERROR',false);//是否打印运行中的 错误 和 消息

define('OUT_CON_CHARSET','UTF-8//IGNORE');//iconv输出字符编码

//判断参数类型是用到的常量
define('IS_NULL' , 	'null');
define('BOOL' , 	'bool');
define('FLOAT' , 	'float');
define('INT' , 		'int');
define('NUMERIC' , 	'numeric');
define('STRING' , 	'string');
define('RESOURCE' , 'resource');
define('IS_ARRAY' , 'array');
define('OBJECT' , 	'object');
define('UNKNOWN' , 	'unknown');

define('SERVICE_PORT' , '8281');//console4php(java)接受端端口 与JAVA端同步修改
define('ADDRESS' , 		'127.0.0.1');//console4php(java)接受端地址
define('S_EOF' , 		'END');//输出结束控制符 与JAVA端同步修改
<?php
	$config_resource_table = 'resource' ;
	$config_node_logic_table = 'node_logic' ;
	$config_unit_table = 'unit' ;
	$config_task_table = 'task' ;
	$config_node_table = 'node' ;
	$config_ke_table = 'ke1' ;
	$config_relationship_table = 'relationship' ;
	
	
	$config_res_title = '列表测试2' ;
	$config_res_content = 'TCP/IP协议栈;网络协议;IPv4;IPv6;传输控制协议;网际协议（互联网协议），是用于报文交换网络的一种面向数据的协议;数据在IP互联网中传送时会被封装为报文或封包;源主机和目标主机' ;
	$config_annatate_mode = 'list' ;
	$config_isLogic = 'no' ;
	$config_logic_user = 2 ;
	$config_ntype = array (
  0 => '术语',
  1 => '知识元',
) ;
	$config_attachAttr = array (
  0 => 
  array (
    'type' => 'nt_1',
    'attr' => 
    array (
      0 => 
      array (
        'title' => 'ke_type',
        'name' => '知识元类型',
        'form' => 'select fixed',
        'set' => 
        array (
          0 => '定义类',
          1 => '分类类',
          2 => '实例类',
          3 => '演化类',
          4 => '属性类',
          5 => '方法类',
          6 => '区别类',
          7 => '其他类',
        ),
      ),
      1 => 
      array (
        'title' => 'core_term',
        'name' => '核心术语',
        'form' => 'select within',
        'set' => 
        array (
          0 => '',
        ),
      ),
      2 => 
      array (
        'title' => 'term_set',
        'name' => '术语集合',
        'form' => 'select within',
        'set' => 
        array (
          0 => '',
        ),
      ),
      3 => 
      array (
        'title' => 'tags',
        'name' => '标签',
        'form' => 'select multi_classified',
        'set' => 
        array (
          0 => '中国',
          1 => '美国',
          2 => '法国',
          3 => '德国',
          4 => '俄罗斯',
          5 => '几内亚',
        ),
      ),
    ),
  ),
) ;
	$config_rtype = array (
  0 => '同义',
  1 => '上位',
  2 => '整体',
  3 => '前序',
  4 => '示例',
  5 => '类比',
  6 => '未知关系',
) ;
	$config_adjacentOffset = 5 ;
?>
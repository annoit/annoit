<?php
	$config_resource_table = 'resource' ;
	$config_node_logic_table = 'node_logic' ;
	$config_unit_table = 'unit' ;
	$config_task_table = 'task' ;
	$config_node_table = 'node' ;
	$config_ke_table = 'ke1' ;
	$config_relationship_table = 'relationship' ;
	
	
	$config_res_title = '模板' ;
	$config_resContent = '模板' ;
	$config_annatate_mode = 'NR' ;
	$config_isLogic = 'yes' ;
	$config_logic_user = 1 ;
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
	        'Set' => '定义类,分类类,实例类,演化类,属性类,方法类,区别类,其他类',
	      ),
	      1 => 
	      array (
	        'title' => 'core_term',
	        'name' => '核心术语',
	        'form' => 'select within',
	        'Set' => '',
	      ),
	      2 => 
	      array (
	        'title' => 'term_set',
	        'name' => '术语集合',
	        'form' => 'select within',
	        'Set' => '',
	      ),
	     	3 => 
	      array (
	        'title' => 'tags',
	        'name' => 'tag',
	        'form' => 'select multi_classified',
	        'Set' => '定义类,分类类,实例类,演化类,属性类,方法类,区别类,其他类',
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
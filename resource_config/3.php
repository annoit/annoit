<?php
	$config_resource_table = 'resource' ;
	$config_node_logic_table = 'node_logic' ;
	$config_unit_table = 'unit' ;
	$config_task_table = 'task' ;
	$config_node_table = 'node' ;
	$config_ke_table = 'ke1' ;
	$config_relationship_table = 'relationship' ;
	
	
	$config_res_title = '测试资源2' ;
	$config_res_content = 'TCP/IP协议栈是指最早发源于美国国防部的ARPA网项目的包含了一系列构成互联网基础的网络协议。

TCP/IP协议栈在字面上主要有：传输控制协议（TCP）和网际协议（IP）。

网际协议（互联网协议），是用于报文交换网络的一种面向数据的协议。

IP协议的独特之处在于：在报文交换网络中主机在传输数据之前，无须与先前未曾通信过的目的主机预先建立好一条特定的“通路”。

数据在IP互联网中传送时会被封装为报文或封包。网际协议提供了一种“不可靠的”数据包传输机制（也被称作“尽力而为”）；也就是说，它不保证数据能准确的传输。数据包在到达的时候可能已经损坏，顺序错乱（与其它一起传送的封包相比），产生冗余包，或者全部丢失。

现在的国际互联网普遍的采用了IP协议。

而现在正在运行中的网际协议典型的代表是IPv4和IPv6。

IPv4使用32位地址，最多可能有4,294,967,296个地址，一般的书写法为4个用小数点分开的十进制数。

IPv6是互联网协议第四版（IPv4）的更新版；最初它在IETF的IPng选取过程中胜出时称为互联网下一代网际协议（IPng），IPv6是被正式广泛使用的第二版互联网协议。

传输控制协议（Transmission Control Protocol）是一种面向连接（连接导向）的，可靠的，基于字节流的运输层（Transport layer）通信协议，由IETF的RFC 793说明。

运输层是指ISO/OSI模型中为源主机和目标主机之间提供可靠的价格合理的透明数据传输的那一层。

工作在运输层的协议主要有TCP协议、UDP协议。

' ;
	$config_annatate_mode = 'NR' ;
	$config_isLogic = 'yes' ;
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
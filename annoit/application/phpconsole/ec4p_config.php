<?php
//����չ����ʽ
define('FORMAT' , 			'format');//��ʽ���ɴ�������ı��ʽ
define('NORMAL' , 			'normal');//�����REMARK��ֻ�ǽ������ӡ����
define('REMARK' , 			'remark');//���ֵ���������ڵ���ȵ����ݣ�����鿴

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

//define('IS_XDEBUG_USER' , TRUE);//�Ƿ�ʹ��xdebug
define('CONSOLE_ENABLE', true);//����̨����Ƿ����
define('P_ERROR',false);//�Ƿ��ӡ�����е� ���� �� ��Ϣ

define('OUT_CON_CHARSET','UTF-8//IGNORE');//iconv����ַ�����

//�жϲ����������õ��ĳ���
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

define('SERVICE_PORT' , '8281');//console4php(java)���ܶ˶˿� ��JAVA��ͬ���޸�
define('ADDRESS' , 		'127.0.0.1');//console4php(java)���ܶ˵�ַ
define('S_EOF' , 		'END');//����������Ʒ� ��JAVA��ͬ���޸�
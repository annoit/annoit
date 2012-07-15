var rtype = ['同义', '上位', '整体', '前序', '示例', '类比', '未知关系'];
var ntype = ['术语', '知识元'];
var attachAttr = [
	{
		type: 'nt_1',
		attr: [
			{
				title: 'ke_type',
				//该title与annotate_model中的ke_type_index相对应，修改时请注意，下同
				name: '知识元类型',
				form: 'select fixed',
				set: ['定义类', '分类类', '实例类', '演化类', '属性类', '方法类', '区别类', '其他类']
				//对应的数据库中的ke_type从1至8
			},
			{
				title: 'core_term',
				name: '核心术语',
				form: 'select within'
			},
			{
				title: 'term_set',
				name: '术语集合',
				form: 'select within'
			},
			{
				title: 'tags',
				name: '标签',
				//form: 'select tag_button',
				form: 'select multi_classified',
				set: ['中国','美国','法国','德国','俄罗斯','几内亚']
				//对应数据库中的tags从0开始
			}
		]
	}
];
var adjacentOffset = 5;
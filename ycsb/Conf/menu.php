<?php
return array(
    'adminuser'  => array(
        'name' => '用户后台管理',
        'step' => 1,
        'menu' =>array(
            '0' => array(
                'linkurl' => '/user/index',
                'name'    => '用户管理',
            ),
            '1' => array(
                'linkurl' => '/user/add',
                'name'    => '添加用户',
            ),
            '2' => array(
                'linkurl' => '/group',
                'name'    => '用户组管理'
            ),
            '3' => array(
                'linkurl' => '/group/add',
                'name'    => '添加用户组'
            ),
        )
    ),
    'xinwen'  => array(
        'name' => '文章管理',
        'step' => 2,
        'menu' =>array(
            '0' => array(
                'linkurl' => '/xinwen/index',
                'name'    => '文章管理',
            ),
            '1' => array(
                'linkurl' => '/xinwen/add',
                'name'    => '添加文章',
            ),
            '2' => array(
                'linkurl' => '/cate/index',
                'name'    => '文章分类'
            ),
            '3' => array(
                'linkurl' => '/cate/add',
                'name'    => '添加分类'
            ),
        )
    ),
    'about'  => array(
        'name' => '关于我们',
        'step' => 3,
        'menu' =>array(
            '1' => array(
                'linkurl' => '/aboutus/index/1',
                'name'    => '关于我们',
            ),
			'2' => array(
                'linkurl' => '/aboutus/index/4',
                'name'    => '联系我们'
            ),
             '5' => array(
                'linkurl' => '/aboutus/siteset',
                'name'    => '网站设置'
            )
        )
    ),
	'message'  => array(
        'name' => '在线预订',
        'step' => 4,
        'menu' =>array(
            '1' => array(
                'linkurl' => '/message',
                'name'    => '在线预订',
            )
        )
    ),
    'link'  => array(
        'name' => '客房服务',
        'step' => 5,
        'menu' =>array(
            '1' => array(
                'linkurl' => '/fangjian',
                'name'    => '客房管理',
            ),
			'2' => array(
                'linkurl' => '/fangjian/add',
                'name'    => '添加客房'
            ),
			'3' => array(
                'linkurl' => '/cate/index/product',
                'name'    => '客房分类'
            ),
			'4' => array(
                'linkurl' => '/cate/add/product',
                'name'    => '添加客房分类'
            )
        )
    )
)

?>
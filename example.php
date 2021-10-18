<?php
include_once './Tree.class.php';
/******模拟从数据库获取数据*********/
$list=[
    [
        'id'=>1,
        'pid'=>0,
        'sum_of_people'=>300,
        'name'=>'乐居公司'
    ],
    [
        'id'=>2,
        'pid'=>1,
        'sum_of_people'=>200,
        'name'=>'开发部'
    ],
    [
        'id'=>3,
        'pid'=>1,
        'sum_of_people'=>60,
        'name'=>'业务部'
    ],
    [
        'id'=>4,
        'pid'=>1,
        'sum_of_people'=>40,
        'name'=>'人事部'
    ],
    [
        'id'=>5,
        'pid'=>2,
        'sum_of_people'=>100,
        'name'=>'后端开发'
    ],
    [
        'id'=>6,
        'pid'=>2,
        'sum_of_people'=>60,
        'name'=>'前端开发'
    ],
    [
        'id'=>7,
        'pid'=>2,
        'sum_of_people'=>40,
        'name'=>'运维部'
    ],
];


$tree_generator = new Tree("id", "pid", "sub_organizations", 'sum_of_people', SORT_ASC, 'path', 'name');
/******1.加载数据*********/
//非引用加载，不会改变原始$list数据，性能低
//$tree_generator->load($list);
//引用加载，会改变原始$list数据，性能高
$tree_generator->loadReference($list);
/******2.使用方法*********/
//功能一、生成root为0的子树
$tree = $tree_generator->deepTree(0);
//功能二、生成root为1子树下的sum_of_people字段所有值以数组形式返回，没有排重，没有排序
//$sum_of_people_list = $tree_generator->deepTreeField('sum_of_people',1);
//功能三、获取id为6的所有父类id
//$parent_list = $tree_generator->deepTreeParent(6);


echo json_encode($tree,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
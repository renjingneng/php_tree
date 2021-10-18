<?php
/******示例*********/
include_once './Tree.class.php';
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
//$tree_generator->load($list);
$tree_generator->loadReference($list);

$tree = $tree_generator->deepTree();

//$tree = $tree_generator->deepTreeField('sum_of_people');


//$tree = $tree_generator->deepTreeParent(6);


echo json_encode($tree);
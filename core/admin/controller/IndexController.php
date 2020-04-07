<?php
namespace core\admin\controller;

use core\base\controller\BaseController;
use core\admin\model\Model;
class IndexController extends BaseController
{
    protected function inputData()
    {
        $db = Model::instance();

        $table = 'teachers';
        $color = ['green','red','blue'];
        $res = $db->get(
            $table,
            [
                'fields' => ['id','name'],
                'where' => ['name' => "Maria"],
                // 'operand' => ['IN'],
                // 'condition' => ['AND','OR'],
                'order' => ['name'],
                'orderDirection' => ['DESC'],
                'limit' => '1',
                'join' => [
                    [
                        'table' => 'joinTable1',
                        'fields' => ['id as jId', 'name as jName'],
                        'type' => 'left',
                        'where' => ['name' => 'sasha'],
                        'operand' => ['='],
                        'condition' => ['OR'],
                        'on' => [
                            'table' => 'teachers',
                            'fields' => ['id', 'parentId']
                        ]
                    ],
                    [
                        'table' => 'joinTable2',
                        'fields' => ['id as j2Id', 'name as j2Name'],
                        'type' => 'left',
                        'where' => ['name' => 'sasha'],
                        'operand' => ['<>'],
                        'condition' => ['OR'],
                        'on' => ['id', 'parentId']
                    ]
                 ]
            ]
        );
        exit();
    }
    
}
?>
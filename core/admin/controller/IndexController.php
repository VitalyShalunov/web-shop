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
                'fields' => [],
                'where' => ['name' => 'Maria, Olya', 'middleName' => 'Andreevna', 'surname' => 'Stavrovskaya', 'car' => 'Porshe', 'color' => $color],
                'operand' => ['IN', 'LIKE%', '<>', '=', 'NOT IN'],
                'condition' => ['AND'],
                'order' => ['surname', 'name'],
                'orderDirection' => ['ASC', 'DESC'],
                'limit' => '',
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
                            'fileds' => ['id', 'parentId']
                        ]
                    ],
                    [
                        'table' => 'joinTable2',
                        'fields' => ['id as j2Id', 'name as j2Name'],
                        'type' => 'left',
                        'where' => ['name' => 'sasha'],
                        'operand' => ['='],
                        'condition' => ['OR'],
                        'on' => [
                            'table' => 'teachers',
                            'fileds' => ['id', 'parentId']
                        ]
                    ]
                ]
            ]
        );
      // var_dump($res);
        exit();
    }
    
}
?>
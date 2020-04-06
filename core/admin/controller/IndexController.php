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
        $res = $db->get($table,
        [
            'fields'=>[],
            'where' => ['surname'=>'Stavrovskaya', 'name'=>'Maria'],
            'operand'=> ['IN','='],
            'condition'=> ['AND'],
            'order'=> ['surname','name'],
            'orderDirection'=> ['ASC','DESC'],
            'limit'=> ''
        ]);
       var_dump($res);
        exit();
    }
    
}
?>
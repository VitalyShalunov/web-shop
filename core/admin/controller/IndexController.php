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
        $files['galleryImg'] = ['green.jpg','red.jpg','blue.jpg'];
        $files['img']='main_img.jpg';
        $res = $db->edit($table,
         [
            'fields' => ['id'=>2 , 'name'=>'Olya']
         ]);
        var_export($res);
        exit();
    }
    
}
?>
<?php
//настройки для проекта
defined('VG_ACCESS') or die('Access denied');

const TEMPLATE = 'templates/default/'; //шаблон
const ADMIN_TEMPLATE = 'core/admin/view/'; //шаблон админки

const COOKIE_VERSION = '1.0.0';

const CRYPT_KEY =''; //ключ шифрования
const COOKIE_TIME = 60; //время бездействия
const BLOCK_TIME = 3;//блокировать, если пробую взломать сайт

const QTY = 8;//количество отображения товаров
const QTY_LINKS = 3; //активно 3 страницы влево-вправо

const ADMIN_CSS_JS = [
    'styles' => [],
    'scripts' => []
];

const USER_CSS_JS = [
    'styles' => [],
    'scripts' => []
];

use core\base\exceptions\RouteException;
function autoloadMainClasses($className)
{
    
    if(!@include_once $className.'.php')
    {
        //var_dump($className.'.php');
        throw new RouteException('Неверное имя файла');
    }
    
}

spl_autoload_register('autoloadMainClasses');
?>
<?php
define('VG_ACCESS', true);//все обращения через index 

header('Content-Type:text/html;charset-utf-8');//заголовок, установление кодировки
session_start();//хранится информация в рамках одной сессии
require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';//хранятся базовые настройки настройки 

//require_once 'core/base/exceptions/RouteException.php';
use core\base\exceptions\RouteException;
use core\base\controller\RouteController;
//require_once 'core/base/controller/RouteController.php';

try {
   // phpinfo();
   //$object = new \ReflectionMethod('core\user\controller\IndexController', "request");
    RouteController::getInstance()->route();
    
} catch (RouteException $e) {
    $e->getMessage();
}
// function   load($className)
// {
//     $className = str_replace('\\', '/', $className);
//     include $className.'.php';
// }
// spl_autoload_register('load');
?>
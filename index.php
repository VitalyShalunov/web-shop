<?php
define('VG_ACCESS', true);//все обращения через index 

header('Content-Type:text/html;charset-utf-8');//заголовок, установление кодировки
session_start();//хранится информация в рамках одной сессии
require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';//хранятся базовые настройки настройки 

use core\base\exceptions\RouteException;
use core\base\controller\RouteController;

try {
    RouteController::instance()->route();
    
} catch (RouteException $e) {
    exit($e->getMessage());
}
?>
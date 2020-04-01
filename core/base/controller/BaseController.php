<?php
namespace core\base\controller;
use core\base\exceptions\RouteException;

//require_once('core/base/exceptions/RouteException.php');
abstract class BaseController
{
    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    public function route()
    {
        $controller = str_replace('/','\\', $this->controller);
        var_dump($controller);
        // $object = new \ReflectionMethod($controller, "request");
        // var_dump($object);
        try {
            $object = new \ReflectionMethod($controller, "request");
            $args = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod
            ];
           
            $object->invoke(new $controller, $args);
        } catch (\ReflectionException $e) {
            echo "heregg";
            throw new RouteException($e->getMessage());
        }
    }

    public function request($args)
    {
        $this->parameters = $args['parameters'];

        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];

        $this->inputData();// $this->$inputData()
        $this->page = $this->outputData();//$this->page = $this->$outputData()

        if($this->errors)
        {
            $this->writeLog();
        }

        $this->getPage();
    }

    protected function render($path = '', $parameters = [])
    {
        extract($parameters);

        if(!$path)
        {
            $path = TEMPLATE.explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0];
            var_dump($path);
        }
    }

    protected function getPage()
    {
        exit($exit->page);
    }
}
?>
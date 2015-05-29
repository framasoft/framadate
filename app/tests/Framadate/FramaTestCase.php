<?php
namespace Framadate;

abstract class FramaTestCase extends \PHPUnit_Framework_TestCase {

    protected function getTestResourcePath($resourcepath) {
        return __DIR__ . '/../resources/'.$resourcepath;
    }

    protected function readTestResource($resourcepath) {
        return file_get_contents($this->getTestResourcePath($resourcepath));
    }

    protected function invoke(&$object, $methodName) {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $reflectionMethod->setAccessible(true);

        $params = array_slice(func_get_args(), 2); // get all the parameters after $methodName
        return $reflectionMethod->invokeArgs($object, $params);
    }

}

<?php
namespace Framadate;

use PHPUnit\Framework\TestCase;

abstract class FramaTestCase extends TestCase {
    protected function getTestResourcePath(string $resourcepath): string
    {
        return __DIR__ . '/../resources/' . $resourcepath;
    }

    protected function readTestResource(string $resourcepath) {
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

<?php

use Mockery as m;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    /**
     * Actually runs a protected method of the given object.
     *
     * @param object $object
     * @param string $method
     * @param mixed  $args
     *
     * @return mixed
     */
    protected function callProtected($object, string $method, $args = [])
    {
        $methodObj = new ReflectionMethod(get_class($object), $method);
        $methodObj->setAccessible(true);

        if (is_object($args)) {
            $args = [$args];
        } else {
            $args = (array) $args;
        }

        return $methodObj->invokeArgs($object, $args);
    }
}

<?php


namespace Adshares\AdsOperator\Tests\Unit;

trait PrivateMethodTrait
{
    /**
     * @param object $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    public function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        try {
            $reflection = new \ReflectionClass(get_class($object));
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);

            return $method->invokeArgs($object, $parameters);
        } catch (\ReflectionException $ex) {
        }
    }
}

<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Trikoder\JsonApiBundle\Controller\JsonApiEnabledInterface;
use Trikoder\JsonApiBundle\Listener\JsonApiEnabledControllerDetectorTrait;

/**
 * Class JsonApiEnabledControllerDetectorTraitTest
 */
class JsonApiEnabledControllerDetectorTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testIsJsonApiEnabledInterface()
    {
        $controller = new JsonApiEnabledInterfaceTestClass();
        $trait = new JsonApiEnabledControllerDetectorTraitTestClass();

        // test controller
        $this->assertTrue($trait->isJsonApiEnabledControllerTest($controller));

        // test callable
        $this->assertTrue($trait->isJsonApiEnabledControllerTest([$controller, 'getSchemaClassMapProvider']));

        // test closure
        try {
            $trait->isJsonApiEnabledControllerTest(function () use ($controller) {
                return $controller;
            });
        } catch (\LogicException $exception) {
            $this->assertEquals('Unsupported type provided as controller', $exception->getMessage());
        }
    }

    public function testResolveControllerFromEventController()
    {
        $controller = new JsonApiEnabledInterfaceTestClass();
        $trait = new JsonApiEnabledControllerDetectorTraitTestClass();

        // test controller
        $this->assertEquals($controller, $trait->resolveControllerFromEventControllerTest($controller));

        // test callable
        $this->assertEquals($controller,
            $trait->resolveControllerFromEventControllerTest([$controller, 'getSchemaClassMapProvider']));

        // test closure
        $this->assertNull($trait->resolveControllerFromEventControllerTest(function () use ($controller) {
            return $controller;
        }));
    }
}

class JsonApiEnabledControllerDetectorTraitTestClass
{
    use JsonApiEnabledControllerDetectorTrait;

    public function isJsonApiEnabledControllerTest($controller)
    {
        return $this->isJsonApiEnabledController($controller);
    }

    public function resolveControllerFromEventControllerTest($controller)
    {
        return $this->resolveControllerFromEventController($controller);
    }
}

class JsonApiEnabledInterfaceTestClass implements JsonApiEnabledInterface
{
    public function getSchemaClassMapProvider()
    {
        // NOOP, stub
    }

    public function getJsonApiConfig()
    {
        // NOOP, stub
    }
}

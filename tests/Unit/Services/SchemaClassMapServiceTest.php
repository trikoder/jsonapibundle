<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Trikoder\JsonApiBundle\Services\SchemaClassMapService;

/**
 * Class RequestDecoderTest
 */
class SchemaClassMapServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $schemaClassMap = new SchemaClassMapService();
        $schemaClassMap->add('someclass', 'someschemaclass');

        $this->assertEquals([
            'someclass' => 'someschemaclass',
        ], $schemaClassMap->getMap());
    }

    public function testAddWithNormalization()
    {
        $schemaClassMap = new SchemaClassMapService();
        $schemaClassMap->add('\someclass', '\someschemaclass');

        $this->assertEquals([
            'someclass' => 'someschemaclass',
        ], $schemaClassMap->getMap());
    }
}

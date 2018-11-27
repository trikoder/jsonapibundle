<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Trikoder\JsonApiBundle\Schema\Autowire\Exception\UnresolvedDependencyException;
use Trikoder\JsonApiBundle\Services\Neomerx\EncoderService;
use Trikoder\JsonApiBundle\Services\SchemaClassMapService;
use Trikoder\JsonApiBundle\Tests\Resources\JsonApi\Schema\Test\PrivateServiceSchema;

class SchemaCreationTest extends KernelTestCase
{
    public function testSchemaWithPrivateService()
    {
        $kernel = static::bootKernel();

        $encoderService = $kernel->getContainer()->get(EncoderService::class);
        $schemaClassMap = new SchemaClassMapService();
        $schemaClassMap->add(\stdClass::class, PrivateServiceSchema::class);

        $this->expectException(UnresolvedDependencyException::class);

        $encoded = $encoderService->encode(
            $schemaClassMap,
            (object) ['id' => 1, 'title' => 'test']
        );
    }
}

<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Trikoder\JsonApiBundle\CompilerPass\SchemaAutoMapCompilerPass;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;

class TestSchemaAutomapCompilerPass extends SchemaAutoMapCompilerPass
{
    protected function getSchemaFilenames(array $schemaDirScanPatterns): array
    {
        return [
            'tests/Resources/AutomapTestSchema/MappableTestSchema.php',
        ];
    }
}

class SchemaAutoMapCompilerPassTest extends TestCase
{
    public function testAutoMap()
    {
        $compilerPass = new TestSchemaAutoMapCompilerPass();
        $containerBuilder = new ContainerBuilder();

        // inject schemaclassmap provider service definition into container builder
        $serviceDefinition = new Definition(SchemaClassMapProviderInterface::class);
        $serviceDefinition->setClass(SchemaClassMapProviderInterface::class);
        $containerBuilder->setDefinition(SchemaClassMapProviderInterface::class, $serviceDefinition);
        $containerBuilder->setParameter('kernel.project_dir', '.');

        $compilerPass->process($containerBuilder);

        // there should be exactly one invocation of add method after this is completed
        $definition = $containerBuilder->getDefinition(SchemaClassMapProviderInterface::class);
        $this->assertNotEmpty($definition->getMethodCalls(), 'There should be classmap method calls in the service definition, but there are none');
        $this->assertCount(1, $definition->getMethodCalls(), 'There should be exactly one call of add method of schema class map provider in service definition');
    }

    public function testSchemaFileFinder()
    {
        $schemaFileFinder = new \ReflectionMethod(SchemaAutoMapCompilerPass::class, 'getSchemaFilenames');
        $schemaFileFinder->setAccessible(true);

        $compilerPass = new SchemaAutoMapCompilerPass();

        // test finding the files
        $schemaFiles = $schemaFileFinder->invoke($compilerPass, ['tests/Resources/AutomapTestSchema/']);

        // there should be at least one entry in the schemafiles array that ends in "MappableTestSchema.php"
        $foundMappableTestSchemaFile = false;
        foreach ($schemaFiles as $schemaFile) {
            if (false !== strpos($schemaFile, 'MappableTestSchema.php')) {
                $foundMappableTestSchemaFile = true;
                break;
            }
        }

        $this->assertTrue($foundMappableTestSchemaFile);
    }

    public function testSchemaFileFinderWithoutDirPattern()
    {
        $schemaFileFinder = new \ReflectionMethod(SchemaAutoMapCompilerPass::class, 'getSchemaFilenames');
        $schemaFileFinder->setAccessible(true);

        $compilerPass = new SchemaAutoMapCompilerPass();

        $schemaFiles = $schemaFileFinder->invoke($compilerPass, []);

        $this->assertEmpty($schemaFiles);
    }
}

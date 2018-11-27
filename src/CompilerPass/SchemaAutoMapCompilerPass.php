<?php

namespace Trikoder\JsonApiBundle\CompilerPass;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Finder\Finder;
use Trikoder\JsonApiBundle\Contracts\SchemaClassMapProviderInterface;
use Trikoder\JsonApiBundle\DependencyInjection\Configuration;
use Trikoder\JsonApiBundle\Schema\AbstractSchema;
use Trikoder\JsonApiBundle\Schema\MappableInterface;

class SchemaAutoMapCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $classMapProviderDefinition = $container->getDefinition(SchemaClassMapProviderInterface::class);

        if (false === (null === $classMapProviderDefinition)) {
            // we need configuration values to determine which folders should we scan for schema files
            $configs = $container->getExtensionConfig('trikoder_json_api');
            $configuration = new Configuration();
            $processor = new Processor();
            $config = $processor->processConfiguration($configuration, $configs);

            $schemaDirScanPatterns = $config['schema_automap_scan_patterns'];

            $schemaFilenames = $this->getSchemaFilenames($schemaDirScanPatterns);

            foreach ($schemaFilenames as $schemaFilename) {
                // we need fqn for reflection and service definition changes
                $schemaFqn = $this->getClassFQNFromFileName($schemaFilename);

                if (false !== $schemaFqn) {
                    if ($this->isSchemaAutoMappable($schemaFqn)) {
                        $this->mapSchema($classMapProviderDefinition, $schemaFqn);
                    }
                }
            }
        }
    }

    /**
     * @return string[]
     */
    protected function getSchemaFilenames(array $schemaDirScanPatterns): array
    {
        if (empty($schemaDirScanPatterns)) {
            return [];
        }

        $schemaFiles = Finder::create()->files()->name('*.php')->ignoreUnreadableDirs()->in($schemaDirScanPatterns);

        $schemaFilenames = [];
        foreach ($schemaFiles as $schemaFile) {
            $schemaFilenames[] = $schemaFile->getRealPath();
        }

        return $schemaFilenames;
    }

    private function mapSchema(Definition $classMapProviderDefinition, string $schemaFqn): Definition
    {
        $mappedClassnames = \call_user_func([$schemaFqn, 'getMappedClassnames']);
        foreach ($mappedClassnames as $mappedClassname) {
            $classMapProviderDefinition->addMethodCall('add', [
                $mappedClassname,
                $schemaFqn,
            ]);
        }

        return $classMapProviderDefinition;
    }

    private function isSchemaAutoMappable(string $schemaFqn): bool
    {
        if (empty($schemaFqn)) {
            return false;
        }

        if (false == class_exists($schemaFqn)) {
            return false;
        }

        $reflection = new \ReflectionClass($schemaFqn);
        $isSchema = $reflection->isSubclassOf(AbstractSchema::class);
        $isMappable = $reflection->implementsInterface(MappableInterface::class);

        // now let's figure out if this class is a schema class and implements methods required for auto mapping
        return $isMappable && $isSchema;
    }

    /**
     * The body of this method was fully lifted (with one line change) from https://github.com/symfony/symfony/blob/master/src/Symfony/Component/Routing/Loader/AnnotationFileLoader.php
     * therefore any credit for it should go to authors of Symfony Routing component
     */
    private function getClassFQNFromFileName($file): string
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));
        if (1 === \count($tokens) && T_INLINE_HTML === $tokens[0][0]) {
            return false;
        }
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }
            if (true === $class && T_STRING === $token[0]) {
                return $namespace . '\\' . $token[1];
            }
            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1]) && \in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING])) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }
            if (T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        break;
                    }
                    if (T_DOUBLE_COLON === $tokens[$j][0] || T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT])) {
                        break;
                    }
                }
                if (!$skipClassToken) {
                    $class = true;
                }
            }
            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }
}

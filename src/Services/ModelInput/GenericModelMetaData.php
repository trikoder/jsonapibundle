<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Services\ModelInput;

use ReflectionClass;
use ReflectionMethod;
use Trikoder\JsonApiBundle\Contracts\ModelTools\ModelMetaDataInterface;

final class GenericModelMetaData implements ModelMetaDataInterface
{
    const GETTER_METHODS_START_WITH = ['get', 'is', 'can'];

    private $modelReflection;

    private $fieldMapping = [];

    public function __construct($modelClass)
    {
        $this->modelReflection = new ReflectionClass($modelClass);
        $this->setFieldMapping();
    }

    public function getAllFields(): array
    {
        return array_keys($this->fieldMapping);
    }

    /**
     * @return null|string
     */
    public function getTypeForField(string $fieldName)
    {
        if (!isset($this->fieldMapping[$fieldName])) {
            return null;
        }

        return $this->fieldMapping[$fieldName]['type'];
    }

    private function setFieldMapping()
    {
        $methods = $this->modelReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!$this->isGetterMethod($method->getName())) {
                continue;
            }

            $fieldName = $this->getFieldName($method->getName());
            $fieldType = $method->hasReturnType() ? $method->getReturnType()->getName() : null;
            $this->fieldMapping[$fieldName] = [
                'name' => $fieldName,
                'type' => $fieldType,
            ];
        }
    }

    private function isGetterMethod(string $methodName): bool
    {
        foreach (self::GETTER_METHODS_START_WITH as $getterStartName) {
            if ($this->startsWith($methodName, $getterStartName)) {
                return true;
            }
        }

        return false;
    }

    private function startsWith(string $methodName, string $startsWith): bool
    {
        return substr($methodName, 0, \strlen($startsWith)) === $startsWith;
    }

    /**
     * Returns field name with stripped *get*, *is*, *can*.
     * If a property with the same name exists returns method name as is (for getters as isActive() {return isActive})
     */
    private function getFieldName(string $methodName): string
    {
        if ($this->propertyWithSameNameExists($methodName)) {
            return $methodName;
        }

        foreach (self::GETTER_METHODS_START_WITH as $getterStartName) {
            if ($this->startsWith($methodName, $getterStartName)) {
                return $this->stripBeginningAndLowerCaseFirstChar($methodName, $getterStartName);
            }
        }
    }

    private function propertyWithSameNameExists(string $methodName): bool
    {
        foreach ($this->modelReflection->getProperties() as $property) {
            if ($property->getName() === $methodName) {
                return true;
            }
        }

        return false;
    }

    private function stripBeginningAndLowerCaseFirstChar(string $methodName, string $getterStartName): string
    {
        return lcfirst(substr($methodName, \strlen($getterStartName)));
    }
}

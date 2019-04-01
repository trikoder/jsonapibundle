<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput;

use PHPUnit\Framework\TestCase;
use Trikoder\JsonApiBundle\Services\ModelInput\GenericModelMetaData;
use Trikoder\JsonApiBundle\Tests\Resources\Entity\GenericModel;

final class GenericModelMetaDataTest extends TestCase
{
    public function testReturnAllFieldsForModel()
    {
        $model = new GenericModel();
        $classMetaData = new GenericModelMetaData($model);
        $this->assertSame(['title', 'isActive', 'approved', 'description', 'canPost', 'date', 'dependentArray', 'role', 'id'], $classMetaData->getAllFields());
        $this->assertNotContains('thisMethodShouldNotBeReturned', $classMetaData->getAllFields());
    }

    public function testReturnCorrectTypesForModel()
    {
        $model = new GenericModel();
        $classMetaData = new GenericModelMetaData($model);
        $this->assertSame('int', $classMetaData->getTypeForField('id'));
        $this->assertSame('string', $classMetaData->getTypeForField('title'));
        $this->assertNull($classMetaData->getTypeForField('description'));
        $this->assertSame('bool', $classMetaData->getTypeForField('isActive'));
        $this->assertSame('bool', $classMetaData->getTypeForField('approved'));
        $this->assertSame('bool', $classMetaData->getTypeForField('canPost'));
        $this->assertSame('DateTime', $classMetaData->getTypeForField('date'));
        $this->assertSame('array', $classMetaData->getTypeForField('dependentArray'));
        $this->assertNull($classMetaData->getTypeForField('role'));
        $this->assertNull($classMetaData->getTypeForField('FieldThatDoesNotExist'));
    }
}

<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput\Traits;

use Neomerx\JsonApi\Document\Error;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\ConstraintViolationToErrorTransformer;

class ConstraintViolationToErrorTransformerTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertViolationToError()
    {
        $trait = new ConstraintViolationToErrorTransformerTraitClass();
        $error = $trait->publicConvertViolationToError($this->getTestViolation(91, 'Constraint violation for test'));

        $this->assertEquals(Error::class, get_class($error));
        $this->assertEquals(91, $error->getCode());
        $this->assertEquals('Constraint violation for test', $error->getTitle());
        $this->assertEquals('Constraint violation "Constraint violation for test"', $error->getDetail());
    }

    private function getTestViolation($code, $message)
    {
        $violationMock = $this->getMockBuilder(ConstraintViolationInterface::class)->disableOriginalConstructor()->getMock();
        $violationMock->method('getCode')->willReturn($code);
        $violationMock->method('getMessage')->willReturn($message);

        return $violationMock;
    }
}

class ConstraintViolationToErrorTransformerTraitClass
{
    use ConstraintViolationToErrorTransformer;

    public function publicConvertViolationToError($violation)
    {
        return $this->convertViolationToError($violation);
    }
}

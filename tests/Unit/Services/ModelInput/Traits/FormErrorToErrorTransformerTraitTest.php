<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput\Traits;

use Neomerx\JsonApi\Document\Error;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\FormErrorToErrorTransformer;

class FormErrorToErrorTransformerTraitTest extends TestCase
{
    public function testFormErrorToError()
    {
        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($this->getTestError('Form error for test'));

        $this->assertEquals(Error::class, \get_class($error));
        $this->assertEquals('Form error for test', $error->getTitle());
        $this->assertEquals('Form error "Form error for test"', $error->getDetail());

        $this->assertEquals([
            'pointer' => '/data/attributes/test',
            'parameter' => 'invalid',
        ], $error->getSource());
    }

    public function testFormErrorToErrorWithCompositeObjectOrigin()
    {
        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($this->getTestErrorWithCompositeOrigin('Form error for test'));

        $this->assertEquals(Error::class, \get_class($error));
        $this->assertEquals('Form error for test', $error->getTitle());
        $this->assertEquals('Form error "Form error for test"', $error->getDetail());

        $this->assertEquals([
            'pointer' => '/data/attributes/rootTest/test',
            'parameter' => 'invalid',
        ], $error->getSource());
    }

    public function testGetCodeFromViolation()
    {
        $trait = new FormErrorToErrorTransformerTraitClass();

        $nullCodeFromEmptyPayload = $trait->publicCodeFromViolation($this->getTestError('Form error for test', ''));
        $nullCodeFromPayloadWithoutCodeKey = $trait->publicCodeFromViolation($this->getTestError('Form error for test', ['key' => 'test']));
        $code = $trait->publicCodeFromViolation($this->getTestError('Form error for test', ['code' => '1000']));

        $this->assertNull($nullCodeFromEmptyPayload);
        $this->assertNull($nullCodeFromPayloadWithoutCodeKey);
        $this->assertEquals('1000', $code);
    }

    private function getTestError($message, $payload = null)
    {
        $constraintMock = $this->getMockBuilder(Constraint::class)->disableOriginalConstructor()->getMock();
        $constraintMock->payload = $payload;

        $formElementMock = $this->getMockBuilder(FormInterface::class)->getMock();
        $formElementMock->method('getName')->willReturn('test');
        $formElementMock->method('isRoot')->willReturn(true);

        $constraintValidationMock = $this->getMockBuilder(ConstraintViolation::class)->disableOriginalConstructor()->getMock();
        $constraintValidationMock->method('getConstraint')->willReturn($constraintMock);
        $constraintValidationMock->method('getPropertyPath')->willReturn('data.test');
        $constraintValidationMock->method('getInvalidValue')->willReturn('invalid');

        $violationMock = $this->getMockBuilder(FormError::class)->disableOriginalConstructor()->getMock();
        $violationMock->method('getMessage')->willReturn($message);
        $violationMock->method('getOrigin')->willReturn($formElementMock);
        $violationMock->method('getCause')->willReturn($constraintValidationMock);

        return $violationMock;
    }

    private function getTestErrorWithCompositeOrigin($message, $payload = null)
    {
        $constraintMock = $this->getMockBuilder(Constraint::class)->disableOriginalConstructor()->getMock();
        $constraintMock->payload = $payload;

        $resourceFormElementMock = $this->getMockBuilder(FormInterface::class)->getMock();
        $resourceFormElementMock->method('getName')->willReturn('resourceTest');
        $resourceFormElementMock->method('isRoot')->willReturn(true);

        $parentFormElementMock = $this->getMockBuilder(FormInterface::class)->getMock();
        $parentFormElementMock->method('getName')->willReturn('rootTest');
        $parentFormElementMock->method('getParent')->willReturn($resourceFormElementMock);
        $parentFormElementMock->method('isRoot')->willReturn(false);

        $formElementMock = $this->getMockBuilder(FormInterface::class)->getMock();
        $formElementMock->method('getName')->willReturn('test');
        $formElementMock->method('isRoot')->willReturn(false);
        $formElementMock->method('getParent')->willReturn($parentFormElementMock);

        $constraintValidationMock = $this->getMockBuilder(ConstraintViolation::class)->disableOriginalConstructor()->getMock();
        $constraintValidationMock->method('getConstraint')->willReturn($constraintMock);
        $constraintValidationMock->method('getPropertyPath')->willReturn('data.test');
        $constraintValidationMock->method('getInvalidValue')->willReturn('invalid');

        $violationMock = $this->getMockBuilder(FormError::class)->disableOriginalConstructor()->getMock();
        $violationMock->method('getMessage')->willReturn($message);
        $violationMock->method('getOrigin')->willReturn($formElementMock);
        $violationMock->method('getCause')->willReturn($constraintValidationMock);

        return $violationMock;
    }
}

class FormErrorToErrorTransformerTraitClass
{
    use FormErrorToErrorTransformer;

    public function publicFormErrorToError($violation)
    {
        return $this->convertFormErrorToError($violation);
    }

    public function publicCodeFromViolation($violation)
    {
        return $this->getCodeFromViolation($violation);
    }
}

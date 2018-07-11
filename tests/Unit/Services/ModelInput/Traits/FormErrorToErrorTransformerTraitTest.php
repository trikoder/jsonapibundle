<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput\Traits;

use Neomerx\JsonApi\Document\Error;
use Symfony\Component\Form\FormError;
use Trikoder\JsonApiBundle\Services\ModelInput\Traits\FormErrorToErrorTransformer;

class FormErrorToErrorTransformerTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testFormErrorToError()
    {
        $trait = new FormErrorToErrorTransformerTraitClass();
        $error = $trait->publicFormErrorToError($this->getTestError('Form error for test'));

        $this->assertEquals(Error::class, get_class($error));
        $this->assertEquals('Form error for test', $error->getTitle());
        $this->assertEquals('Form error "Form error for test"', $error->getDetail());
    }

    private function getTestError($message)
    {
        $violationMock = $this->getMockBuilder(FormError::class)->disableOriginalConstructor()->getMock();
        $violationMock->method('getMessage')->willReturn($message);

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
}

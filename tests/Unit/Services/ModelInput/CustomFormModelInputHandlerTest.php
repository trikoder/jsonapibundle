<?php

namespace Trikoder\JsonApiBundle\Tests\Unit\Services\ModelInput;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Form\FormInterface;
use Trikoder\JsonApiBundle\Services\ModelInput\CustomFormModelInputHandler;

class CustomFormModelInputHandlerTest extends TestCase
{
    public function testFormCallChain()
    {
        // prepare mocks
        $formMock = $this->getMockBuilder(FormInterface::class)->getMock();
        $testModel = new \stdClass();
        $testModel->test = true;

        // we need chain call to happen
        $formMock->expects($this->once())->method('setData')->willReturn($formMock);
        $formMock->expects($this->once())->method('submit')->willReturn($formMock);
        $formMock->expects($this->once())->method('getData')->willReturn($testModel);

        // trigger
        $handler = new CustomFormModelInputHandler($formMock);
        $handler->forModel($testModel)->handle([])->getResult();
    }
}

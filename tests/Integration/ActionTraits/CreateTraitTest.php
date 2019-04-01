<?php

namespace Trikoder\JsonApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Trikoder\JsonApiBundle\Controller\Traits\Actions\CreateTrait;

class CreateTraitTest extends KernelTestCase
{
    public function testHandleCreateModelInputFromRequest()
    {

    }

    public function testValidateCreatedModel()
    {

    }

    public function testCreateModelFromRequest()
    {

    }

    public function testCreateCreatedFromRequest()
    {

    }

    private function getTraitMock()
    {
        $mock = $this->getMockForTrait(CreateTrait::class);

        $mock->method('getRouter')
            ->will();
    }

    private function getConfig()
    {

    }

    private function getRouter()
    {
        return $this->getMockBuilder(RouterInterface::class)->getMock();
    }
}

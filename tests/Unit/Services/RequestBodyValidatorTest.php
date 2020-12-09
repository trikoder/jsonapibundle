<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Unit\Services;

use Prophecy\Argument;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Trikoder\JsonApiBundle\Services\RequestDecoder\SymfonyValidatorAdapter;

final class RequestBodyValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsEmptyViolationListWithoutDoingValidationIfPrimaryDataIsNull()
    {
        $symfonyValidator = $this->prophesize(ValidatorInterface::class);
        $validator = $this->createService($symfonyValidator->reveal());

        $result = $validator->validate(['data' => null]);

        $this->assertCount(0, $result);
        $symfonyValidator->validate()->shouldNotHaveBeenCalled();
    }

    public function testValidationIsBeingPerformedWhenValidInputIsProvided()
    {
        $testData = [
            'data' => [
                'id' => 'foo',
                'type' => 'bar',
            ],
        ];

        $symfonyValidator = $this->prophesize(ValidatorInterface::class);

        $validator = $this->createService($symfonyValidator->reveal());

        $validator->validate($testData);

        $symfonyValidator->validate(
            Argument::type('array'),
            Argument::type(Constraint::class)
        )->shouldHaveBeenCalledOnce();
    }

    protected function createService(ValidatorInterface $validator)
    {
        return new SymfonyValidatorAdapter($validator);
    }
}

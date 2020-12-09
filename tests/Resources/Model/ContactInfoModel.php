<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\Callback(callback="alwaysInvalidPhoneNumber", groups={"alwaysInvalidPhoneNumber"}) Always invalid trigger used in tests
 */
class ContactInfoModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min="1", max="10")
     */
    private $label = '';

    /**
     * @var PhoneNumberModel
     *
     * @Assert\Valid
     */
    private $phoneNumber;

    public function alwaysInvalidPhoneNumber(ExecutionContextInterface $context, $payload)
    {
        $context->buildViolation('This is invalid')
            ->atPath('phoneNumber.number')
            ->setInvalidValue($this->phoneNumber->getNumber())
            ->addViolation();
    }

    public function __construct()
    {
        $this->phoneNumber = new PhoneNumberModel();
    }

    /**
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     */
    public function getPhoneNumber(): PhoneNumberModel
    {
        return $this->phoneNumber;
    }

    /**
     */
    public function setPhoneNumber(PhoneNumberModel $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}

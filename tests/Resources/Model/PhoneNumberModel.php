<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Model;

use Symfony\Component\Validator\Constraints as Assert;

class PhoneNumberModel
{
    /**
     * @var string
     *
     * @Assert\Length(min="2")
     */
    private $areaCode = '';

    /**
     * @var string
     *
     * @Assert\Length(min="5")
     */
    private $number = '';

    /**
     * @var int
     */
    private $intNumber = 0;

    /**
     * @var string
     */
    private $numberWithValidationOnlyOnForm = 0;

    /**
     */
    public function getAreaCode(): string
    {
        return $this->areaCode;
    }

    /**
     */
    public function setAreaCode(string $areaCode): void
    {
        $this->areaCode = $areaCode;
    }

    /**
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getIntNumber(): int
    {
        return $this->intNumber;
    }

    public function setIntNumber(int $intNumber): void
    {
        $this->intNumber = $intNumber;
    }

    public function getNumberWithValidationOnlyOnForm(): string
    {
        return $this->numberWithValidationOnlyOnForm;
    }

    public function setNumberWithValidationOnlyOnForm(string $numberWithValidationOnlyOnForm)
    {
        $this->numberWithValidationOnlyOnForm = $numberWithValidationOnlyOnForm;
    }
}

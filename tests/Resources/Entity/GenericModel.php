<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\Entity;

use DateTime;

final class GenericModel extends BaseEntity
{
    use ResourceId;

    private $title;
    private $description;
    private $isActive = false;
    private $approved = false;
    private $canPost = false;
    private $date;
    private $dependentArray = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $active)
    {
        $this->isActive = $active;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function thisMethodShouldNotBeReturned()
    {
    }

    public function canPost(): bool
    {
        return $this->canPost;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function getDependentArray(): ?array
    {
        return $this->dependentArray;
    }
}

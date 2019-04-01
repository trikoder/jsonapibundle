<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Tests\Resources\Entity;

trait ResourceId
{
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}

<?php

declare(strict_types=1);

namespace Trikoder\JsonApiBundle\Contracts;

final class RelationshipDoesNotExistException extends \Exception
{
    public function __construct($relationship)
    {
        parent::__construct(sprintf('Relationship "%s" does not exist', $relationship));
    }
}

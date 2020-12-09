<?php

namespace Trikoder\JsonApiBundle\Schema\Builtin;

interface ResourceInterface
{
    public static function getJsonApiResourceType(): string;
}

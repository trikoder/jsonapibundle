<?php

namespace Trikoder\JsonApiBundle\Services\Neomerx;

use Closure;
use Neomerx\JsonApi\Schema\ResourceObject as BaseResourceObject;

class ResourceObject extends BaseResourceObject
{
    public function getAttributes()
    {
        $attributes = parent::getAttributes();

        array_walk($attributes, function (&$value, $key) {
            if ($value instanceof Closure) {
                $value = $value();
            }
        });

        return $attributes;
    }
}

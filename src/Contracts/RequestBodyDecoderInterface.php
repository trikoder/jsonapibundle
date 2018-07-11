<?php

namespace Trikoder\JsonApiBundle\Contracts;

interface RequestBodyDecoderInterface
{
    /**
     * @param array $body
     *
     * @return null|array
     */
    public function decode(array $body = null);
}

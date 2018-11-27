<?php

namespace Trikoder\JsonApiBundle\Response;

/**
 * Class Response
 */
abstract class AbstractResponse
{
    /** @var array */
    private $meta = [];

    /** @var array */
    private $links = [];

    /**
     * AbstractResponse constructor.
     */
    public function __construct(array $meta = [], array $links = [])
    {
        $this->meta = $meta;
        $this->links = $links;
    }

    /**
     * @param $meta
     * @param $value
     */
    public function addMeta($meta, $value): self
    {
        $this->meta[$meta] = $value;

        return $this;
    }

    /**
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param $link
     * @param $value
     */
    public function addLink($link, $value): self
    {
        $this->links[$link] = $value;

        return $this;
    }
}
